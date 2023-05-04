<?php

declare(strict_types=1);

namespace App\Service;

use App\Manager\AbstractManager;

/**
 * Validator validates the values of a submitted form.
 */
class Validator
{
    private array $data = [];

    private array $errors = [];

    private array $messages = [
        'exist' => 'Un champ obligatoire est manquant dans le formulaire.',
        'empty' => 'Ce champ ne peut être vide.',
        'password' => "Ce champ n'est pas un mot de passe valide : il doit comporter au moins un chiffres, au moins une lettre et au moins 8 caractères.",
        'length' => 'La longueur du champ doit être comprise entre %s et %s caractères.',
        'slug' => 'Un slug ne doit pas comporter de chiffres ou de caractères spéciaux.',
        'equal' => 'La valeur de ce champ doit être identique à celle du champ %s',
        'mail' => 'Ce champ doit comporter un email valide suivant le format nom@domaine.fr',
        'unique' => 'La valeur de ce champ est déjà utilisée en base de données.',
        'select' => "L'option choisie pour ce champ n'est pas valide.",
    ];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validates a password.
     */
    public function checkPassword(string $key): self
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];

            return $this;
        }

        if (!preg_match('/^(?=.*[0-9])(?=.*[a-z]).{8,20}$/', $this->data[$key])) {
            $this->errors[$key][] = $this->messages['password'];
        }

        return $this;
    }

    /**
     * Checks the length of a string.
     */
    public function checkLength(string $key, int $min = 3, int $max = 255): self
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];

            return $this;
        }

        $length = strlen($this->data[$key]);

        if ($length < $min || $length > $max) {
            $this->errors[$key][] = sprintf($this->messages['length'], $min, $max);
        }

        return $this;
    }

    /**
     * Checks if a string is a valid slug.
     */
    public function slug(string $key): self
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];

            return $this;
        }

        $pattern = '/^[a-z]+(-?[a-z]+)+$/';
        if (!preg_match($pattern, $this->data[$key])) {
            $this->errors[$key][] = $this->messages['slug'];
        }

        return $this;
    }

    /**
     * Checks if a field is empty.
     */
    public function notEmpty(string $key): self
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];
        } elseif (empty($this->data[$key])) {
            $this->errors[$key][] = $this->messages['empty'];
        }

        return $this;
    }

    /**
     * Checks if a field is equal to another field.
     */
    public function equal(string $key, string $anotherKey, string $otherFieldName): self
    {
        $error = false;

        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];
            $error = true;
        }

        if (!isset($this->data[$anotherKey])) {
            $this->errors[$anotherKey][] = $this->messages['exist'];
            $error = true;
        }

        if ($error) {
            return $this;
        } elseif ($this->data[$key] !== $this->data[$anotherKey]) {
            $this->errors[$key][] = sprintf($this->messages['equal'], $otherFieldName);
        }

        return $this;
    }

    /**
     * Checks if a string is a valid mail.
     */
    public function checkMail(string $key): self
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];
        } elseif (!preg_match("#^[a-z0-9-_.]+@[a-z0-9-_.]{2,}\.[a-z]{2,4}$#", $this->data[$key])) {
            $this->errors[$key][] = $this->messages['mail'];
        }

        return $this;
    }

    /**
     * Checks if a value exist in the database.
     */
    public function isUnique(string $key, string $propertyName, AbstractManager $manager): self
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];

            return $this;
        }

        $total = $manager->count("$propertyName = :field", ['field' => $this->data[$key]]);
        if (0 !== $total) {
            $this->errors[$key][] = $this->messages['unique'];
        }

        return $this;
    }

    /**
     * Checks if the selected value is a valid choice.
     */
    public function selectIsValid(string $key, array $validOptions): self
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = $this->messages['exist'];

            return $this;
        }

        if (!in_array($this->data[$key], $validOptions)) {
            $this->errors[$key][] = $this->messages['select'];
        }

        return $this;
    }

    /**
     * Gets the value of error.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
