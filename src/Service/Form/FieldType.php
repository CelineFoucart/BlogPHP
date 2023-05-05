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
final class FieldType extends AbstractType
{
    /**
     * The field type, accepted types: text, textarea, email, password, number, checkbox.
     */
    private string $type;

    /**
     * The value of the HTML attribute placeholder.
     */
    private string $placeholder = '';

    /**
     * The value of the HTML attribute rows for textarea.
     */
    private int $textareaRows = 5;

    /**
     * @param string $name     the field name, also used for the id
     * @param string $type     the field type
     * @param bool   $required if the field is required
     */
    public function __construct(string $name, string $type = 'text', bool $required = true)
    {
        parent::__construct($name, $required);
        $this->setType($type);
        if ('checkbox' === $type) {
            $this->inputClass = 'form-check-input';
        }
    }

    /**
     * Sets the value of type, accepted types: text, textarea, email, password, number.
     */
    public function setType(string $type): self
    {
        $acceptedTypes = ['text', 'textarea', 'email', 'password', 'number', 'checkbox'];

        if (!in_array($type, $acceptedTypes)) {
            $type = 'text';
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Sets the value of placeholder.
     */
    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Sets the value of textareaRows.
     */
    public function setTextareaRows(int $textareaRows): self
    {
        $this->textareaRows = $textareaRows;

        return $this;
    }

    /**
     * Renders the field with a label and the serror section.
     */
    public function render(): string
    {
        $errorDiv = $this->getErrorAsHTML();

        if ('textarea' === $this->type) {
            $field = $this->getFieldAsTextarea();
            $label = $this->getFormattedLabel();
        } elseif ('checkbox' === $this->type) {
            $label = $this->getFormattedLabel('form-check-label');
            $field = $this->getFieldAsInput();
        } else {
            $field = $this->getFieldAsInput();
            $label = $this->getFormattedLabel();
        }

        if ('checkbox' === $this->type) {
            return '<div class="mb-3 form-check form-switch">'.$field.$label.$errorDiv.'</div>';
        }

        return '<div class="mb-3">'.$label.$field.$errorDiv.'</div>';
    }

    /**
     * Renders a textarea field.
     */
    private function getFieldAsTextarea(): string
    {
        $required = ($this->required) ? 'required' : '';
        $inputClass = $this->getInputClasses();
        $placeholder = (strlen($this->placeholder) > 1) ? ' placeholder="'.$this->placeholder.'"' : '';

        return '<textarea class="'.$inputClass.'" id="'.$this->id.'" name="'.$this->name.'"
            '.$placeholder.' rows="'.$this->textareaRows.'" '.$required.'>'.$this->value.'</textarea>'
        ;
    }

    /**
     * Renders a input field.
     */
    private function getFieldAsInput(): string
    {
        $required = ($this->required) ? 'required' : '';
        $inputClass = $this->getInputClasses();

        if (is_bool($this->value)) {
            $data = ($this->value) ? ' checked ' : '';
        } else {
            $data = ' value="'.$this->value.'" ';
        }

        $placeholder = (strlen($this->placeholder) > 1) ? ' placeholder="'.$this->placeholder.'"' : '';

        return '<input type="'.$this->type.'" class="'.$inputClass.'" id="'.$this->id.'" name="'.$this->name.'" 
            '.$placeholder.$data.$required.'>';
    }
}
