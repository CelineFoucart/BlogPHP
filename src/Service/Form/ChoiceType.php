<?php

declare(strict_types=1);

namespace App\Service\Form;

/**
 * Class FieldType.
 *
 * This class represents a select field in a form with a label, an error section and the field.
 *
 * Usage:
 * ```php
 *  $select = (new Field("category", true))->setOptions($categories)->setValue(1)->render();
 *  ```
 */
final class ChoiceType extends AbstractType
{
    private array $options = [];

    private bool $isMultiple = false;
    
    public function __construct(string $name, bool $required = true)
    {
        parent::__construct($name, $required);
        $this->inputClass = "form-select";
    }

    public function render(): string
    {
        $errorDiv = $this->getErrorAsHTML();
        $select = $this->renderSelect();
        $label = $this->getFormattedLabel();

        return '<div class="mb-3">'.$label.$select.$errorDiv.'</div>';
    }

    /**
     * Get the value of options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set the value of options.
     *
     * @param array $options an array of object with an id property that can be converted to string
     */
    public function setOptions(array $entities): self
    {
        $options = [];

        foreach ($entities as $entity) {
            $options[$entity->getId()] = (string) $entity;
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get the value of isMultiple.
     */
    public function getIsMultiple(): bool
    {
        return $this->isMultiple;
    }

    /**
     * Defined if the select is multiple or not.
     */
    public function setIsMultiple(bool $isMultiple): self
    {
        $this->isMultiple = $isMultiple;

        return $this;
    }

    /**
     * Render the select field as HTML.
     *
     * @return string
     */
    private function renderSelect(): string
    {
        $required = ($this->required) ? 'required' : '';
        $inputClass = $this->getInputClasses();
        $html = '<select  class="'.$inputClass.'" id="'.$this->id.'" name="'.$this->name.'"' .$required.'>';

        foreach ($this->options as $id => $option) {
            $selected = ((int)$id === (int)$this->value) ? 'selected' : '';
            $html .= '<option value="'.$id.'" '.$selected.'>'.$option.'</option>';
        }

        $html .= '</select>';

        return $html;
    }
}
