<?php

declare(strict_types=1);

namespace App\Service\Form;

use Exception;

/**
 * Class FormBuilder.
 *
 * This class builds a form.
 *
 * Usage:
 *  ```php
 *  $form = (new FormBuilder())->addField('pseudo', 'text')->renderForm();
 *  $formWithClass = (new FormBuilder())->addField('pseudo', 'text')->setFormClasses('form')->renderForm();
 *  $formWithData = (new FormBuilder())->addField('pseudo', 'text')->setData($data)->renderForm();
 *  $formWithError = (new FormBuilder())->addField('pseudo', 'text')->setErrors($error)->renderForm();
 *  ```
 */
class FormBuilder
{
    /**
     * The fields errors.
     */
    private array $errors = [];

    /**
     * An array of data for the field.
     */
    private array $data = [];

    /**
     * @var FieldType[]|ChoiceType[] an array of fieldType objects
     */
    private array $fields = [];

    /**
     * The method of the field, POST or GET.
     */
    private string $method;

    /**
     * The action of the form.
     */
    private string $action = '';

    /**
     * The form classes.
     */
    private string $formClasses = '';

    private array $button = [
        'class' => 'btn btn-primary',
        'text' => 'Soumettre',
    ];

    public function __construct(string $method = 'POST')
    {
        $this->setMethod($method);
    }

    /**
     * Adds a field to the form.
     *
     * @param array $options the field options (required, placeholder, label, class, errorClass, errorSectionClass, options for select fields)
     */
    public function addField(string $name, string $type = 'text', array $options = []): self
    {
        $isRequired = (isset($options['required'])) ? $options['required'] : true;

        if ('select' === $type) {
            $field = new ChoiceType($name, $isRequired);

            if (isset($options['options'])) {
                $field->setOptions($options['options']);
            }
        } else {
            $field = new FieldType($name, $type, $isRequired);
        }

        $field = $this->definedFieldOptions($field, $options);

        if (isset($this->data[$name])) {
            $field->setValue($this->data[$name]);
        }

        if (isset($this->errors[$name])) {
            $errors = join('<br>', $this->errors[$name]);
            $field->defineAdInvalid($errors);
        }

        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * Renders the form.
     *
     * @throws Exception if there is no field
     */
    public function renderForm(?string $csrfToken = null): string
    {
        if (empty($this->fields)) {
            throw new Exception('The form must have at least one field.');
        }
        $formParts = $this->getFormParts($csrfToken);
        $fields = join('', $formParts['fields']);

        return $formParts['start'].$formParts['errorBlock'].$fields.$formParts['button'].$formParts['end'];
    }

    /**
     * Generates form parts.
     */
    public function getFormParts(?string $csrfToken = null): array
    {
        $fields = [];
        foreach ($this->fields as $key => $field) {
            $fields[$key] = $field->render();
        }

        if ($csrfToken) {
            $fields['csrf'] = '<input type="hidden" name="_csrf" value="'.$csrfToken.'">';
        }

        return [
            'start' => '<form action="'.$this->action.'" method="'.$this->method.'" class="'.$this->formClasses.'">',
            'errorBlock' => $this->formatErrorGeneralBlock(),
            'fields' => $fields,
            'button' => '<button type="submit" class="'.$this->button['class'].'">'.$this->button['text'].'</button>',
            'end' => '</form>',
        ];
    }

    /**
     * Sets the value of errors.
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Sets the value of method.
     */
    public function setMethod(string $method): self
    {
        $method = strtoupper($method);

        if (!in_array($method, ['POST', 'GET'])) {
            $method = 'POST';
        }
        $this->method = $method;

        return $this;
    }

    /**
     * Sets the value of action.
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Sets the value of formClasses.
     */
    public function setFormClasses(string $formClasses): self
    {
        $this->formClasses = $formClasses;

        return $this;
    }

    /**
     * Sets the value of button.
     */
    public function setButton(string $title = 'Envoyer', string $class = 'btn btn-primary'): self
    {
        $this->button = ['text' => $title, 'class' => $class];

        return $this;
    }

    /**
     * Sets the value of data.
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Generates the error block, to the top of the form.
     */
    private function formatErrorGeneralBlock(): string
    {
        if (empty($this->errors)) {
            return '';
        }

        $message = '<p><strong>Ce formulaire comporte des erreurs.</strong></p>';
        $errorsWithNotFields = [];

        foreach ($this->errors as $key => $error) {
            if (!isset($this->fields[$key])) {
                if (is_array($error)) {
                    $error = join('<br>', $error);
                }
                $errorsWithNotFields[] = $error;
            }
        }

        $otherMessages = (!empty($errors)) ? '<p>'.join('<br>', $errorsWithNotFields).'</p>' : '';

        return '<div class="alert alert-danger">'.$message.$otherMessages.'</div>';
    }

    /**
     * Sets the options of a field.
     *
     * @param FieldType|ChoiceType $field
     *
     * @return FieldType|ChoiceType
     */
    private function definedFieldOptions($field, array $options = [])
    {
        if (isset($options['placeholder'])) {
            $field->setPlaceholder($options['placeholder']);
        }

        if (isset($options['label'])) {
            $field->setLabel($options['label']);
        }

        if (isset($options['class'])) {
            $field->setInputClass($options['class']);
        }

        if (isset($options['errorClass'])) {
            $field->setErrorClass($options['errorClass']);
        }

        if (isset($options['errorSectionClass'])) {
            $field->setErrorSectionClass($options['errorSectionClass']);
        }

        if (isset($options['rows'])) {
            $field->setTextareaRows($options['rows']);
        }

        return $field;
    }
}
