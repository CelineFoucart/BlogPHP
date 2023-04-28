<?php

declare(strict_types=1);

namespace App\Service\Form;

/**
 * Abstract Class FieldType.
 *
 * This class represents a field in a form with a label, an error section and the field.
 */
abstract class AbstractType
{
    /**
     * The value of the HTML attribute name.
     */
    protected string $name;

    /**
     * The value of the HTML attribute id.
     */
    protected string $id;

    /**
     * The value of the field label.
     */
    protected ?string $label = null;

    /**
     * Define if the field is required.
     */
    protected bool $required;

    /**
     * Define if the field has errors.
     */
    protected bool $hasError = false;

    /**
     * The error message to display.
     */
    protected string $errorMessage = "Ce champ n'est pas valide";

    /**
     * The input of textarea classes.
     */
    protected string $inputClass = 'form-control';

    /**
     * The error class for the input or textarea.
     */
    protected string $errorClass = 'is-invalid';

    /**
     * The error section class.
     */
    protected string $errorSectionClass = 'invalid-feedback';

    /**
     * The value of the field.
     */
    protected mixed $value = '';

    public function __construct(string $name, bool $required = true)
    {
        $this->name = $name;
        $this->id = $name;
        $this->required = $required;
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
     * Set the value of input class.
     */
    public function setInputClass(string $inputClass): self
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    /**
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
     * Set the value of value.
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Render the field with a label and the serror section.
     */
    abstract public function render(): string;

    /**
     * Render the label of the field.
     */
    protected function getFormattedLabel(?string $labelClass = null): string
    {
        $label = ($this->label) ? $this->label : ucfirst($this->name);
        if ($this->required) {
            $label .= '<sup>*</sup>';
        }

        if ($labelClass) {
            $class = 'class="'.$labelClass.'"';
        } else {
            $class = '';
        }

        return '<label '.$class.' for="'.$this->id.'">'.$label.'</label>';
    }

    /**
     * Render the input or textarea classes.
     */
    protected function getInputClasses(): string
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
    protected function getErrorAsHTML(): string
    {
        $error = '';

        if ($this->hasError) {
            $error .= '<div class="'.$this->errorSectionClass.'">'.$this->errorMessage.'</div>';
        }

        return $error;
    }
}
