<?php

declare(strict_types=1);

namespace App\Service\Form;

/**
 * Class FieldType.
 *
 * This class represents a field in a form with a label, an error section and the field, an input or a textarea.
 * This class does not render a select.
 *
 * Usage:
 * ```php
 *  $invalidField = (new Field("pseudo"))->defineAdInvalid()->render();
 *  $validField = (new Field("email", "email"))->render();
 *  $notRequiredField = (new Field("firstName", "text", false))->render();
 *  $fieldWithValue = (new Field("title", "text"))->setValue("Lorem Ipsum")->render();
 *  ```
 */
class FieldType
{
    /**
     * The field type, accepted types: text, textarea, email, password, number.
     */
    private string $type;

    /**
     * The value of the HTML attribute name.
     */
    private string $name;

    /**
     * The value of the HTML attribute id.
     */
    private string $id;

    /**
     * The value of the field label.
     */
    private ?string $label = null;

    /**
     * Define if the field is required.
     */
    private bool $required;

    /**
     * The value of the HTML attribute placeholder.
     */
    private string $placeholder = '';

    /**
     * The value of the field.
     */
    private string $value = '';

    /**
     * Define if the field has errors.
     */
    private bool $hasError = false;

    /**
     * The error message to display.
     */
    private string $errorMessage = "Ce champ n'est pas valide";

    /**
     * The input of textarea classes.
     */
    private string $inputClass = 'form-control';

    /**
     * The error class for the input or textarea.
     */
    private string $errorClass = 'is-invalid';

    /**
     * The error section class.
     */
    private string $errorSectionClass = 'invalid-feedback';

    /**
     * The value of the HTML attribute rows for textarea.
     */
    private int $textareaRows = 5;

    public function __construct(string $name, string $type = 'text', bool $required = true)
    {
        $this->name = $name;
        $this->id = $name;
        $this->setType($type);
        $this->required = $required;
    }

    /**
     * Set the value of type, accepted types: text, textarea, email, password, number.
     */
    public function setType(string $type): self
    {
        $acceptedTypes = ['text', 'textarea', 'email', 'password', 'number'];

        if (!in_array($type, $acceptedTypes)) {
            $type = 'text';
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Set the value of value.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the value of id.
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Define the field as invalid.
     */
    public function defineAdInvalid(string $message = "Ce champ n'est pas valide"): self
    {
        $this->hasError = true;
        $this->errorMessage = $message;

        return $this;
    }

    /**
     * Set the value of label.
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the value of placeholder.
     */
    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }
    
    /**
     * Set the value of input class.
     */
    public function setInputClass(string $inputClass): self
    {
        $this->inputClass = $inputClass;

        return $this;
    }/**
     * Set the value of errorClass.
     */
    public function setErrorClass(string $errorClass): self
    {
        $this->errorClass = $errorClass;

        return $this;
    }

    /**
     * Set the value of errorSectionClass.
     */
    public function setErrorSectionClass(string $errorSectionClass): self
    {
        $this->errorSectionClass = $errorSectionClass;

        return $this;
    }

    /**
     * Render the field with a label and the serror section.
     */
    public function render(): string
    {
        $label = $this->getFormattedLabel();

        if ('textarea' === $this->type) {
            $field = $this->getFieldAsTextarea();
        } else {
            $field = $this->getFieldAsInput();
        }

        $errorDiv = $this->getErrorAsHTML();

        return '<div class="mb-3">'.$label.$field.$errorDiv.'</div>';
    }

    /**
     * Render the label of the field.
     */
    private function getFormattedLabel(): string
    {
        $label = ($this->label) ? $this->label : ucfirst($this->name);
        if ($this->required) {
            $label .= '<sup>*</sup>';
        }

        return '<label for="'.$this->id.'">'.$label.'</label>';
    }

    /**
     * Render a textarea field.
     */
    private function getFieldAsTextarea(): string
    {
        $required = ($this->required) ? 'required' : '';
        $inputClass = $this->getInputClasses();

        return '<textarea class="'.$inputClass.'" id="'.$this->id.'" name="'.$this->name.'"
            placeholder="'.$this->placeholder.'" rows="'.$this->textareaRows.'" '.$required.'>'.$this->value.'</textarea>'
        ;
    }

    /**
     * Render a input field.
     */
    private function getFieldAsInput(): string
    {
        $required = ($this->required) ? 'required' : '';
        $inputClass = $this->getInputClasses();

        return '<input type="'.$this->type.'" class="'.$inputClass.'" id="'.$this->id.'" name="'.$this->name.'" 
            placeholder="'.$this->placeholder.'" value="'.$this->value.'" '.$required.'>';
    }

    /**
     * Render the input or textarea classes.
     */
    private function getInputClasses(): string
    {
        $inputClass = $this->inputClass;
        if ($this->hasError) {
            $inputClass .= ' '.$this->errorClass;
        }

        return $inputClass;
    }

    /**
     * Render the error in a HTML element.
     */
    private function getErrorAsHTML(): string
    {
        $error = '';

        if ($this->hasError) {
            $error .= '<div class="'.$this->errorSectionClass.'">'.$this->errorMessage.'</div>';
        }

        return $error;
    }
}
