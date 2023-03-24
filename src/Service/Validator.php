<?php

declare(strict_types=1);

namespace App\Service;

class Validator
{
    private array $data = [];
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate a password.
     */
    public function checkPassword(string $key, string $field = 'Ce champ'): bool
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = "$key n'existe pas";

            return false;
        }

        if (!preg_match('/^(?=.*[0-9])(?=.*[a-z]).{8,20}$/', $this->data[$key])) {
            $this->errors[$key][] = "$field n'est pas un mot de passe valide";

            return false;
        }

        return true;
    }

    /**
     * Check the length of a string.
     */
    public function checkLength(string $key, int $size, string $field = 'Ce champ'): bool
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = "$key n'existe pas";

            return false;
        }

        if (strlen($this->data[$key]) > $size) {
            return true;
        } else {
            $this->errors[$key][] = "$field doit faire plus que $size caractères";
        }

        return false;
    }

    /**
     * Check if a string is a valid slug.
     *
     * @param mixed string
     */
    public function slug(string $key, string $field = 'Ce champ'): bool
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = "$key n'existe pas";

            return false;
        }
        $pattern = '/^[a-z]+(-?[a-z]+)+$/';
        if (preg_match($pattern, $this->data[$key])) {
            return true;
        } else {
            $this->errors[$key][] = 'Un slug ne doit pas comporter de chiffres ou de caractères spéciaux';

            return false;
        }
    }

    public function exist(string $key, string $field = 'Ce champ'): bool
    {
        if (!isset($this->data[$key])) {
            $this->errors[$key][] = "$field est vide";

            return false;
        } else {
            if (empty($this->data[$key])) {
                $this->errors[$key][] = "$field ne peut être vide";

                return false;
            }
        }

        return true;
    }

    /**
     * Compare an element to another value.
     */
    public function equal(string $key, $valueToCompare, string $field = 'Ce champ'): bool
    {
        if (isset($this->data[$key])) {
            $valueCompared = (is_int($valueToCompare)) ? (int) $this->data[$key] : $this->data[$key];
            if ($valueCompared === $valueToCompare) {
                $this->errors[$key][] = "$field n'est pas valide";

                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Check if a string is a valid mail.
     */
    public function checkMail(string $key, string $field = 'Ce champ'): bool
    {
        if (!$this->exist($key)) {
            $this->errors[$key][] = "$field n'est pas un mail";

            return false;
        }
        if (!preg_match("#^[a-z0-9-_.]+@[a-z0-9-_.]{2,}\.[a-z]{2,4}$#", $this->data[$key])) {
            $this->errors[$key][] = "$field n'est pas un mail";

            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the value of error.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
