<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Mailer formats and sends an email.
 */
class Mailer
{
    /**
     * @var array the mail headers parts
     */
    private array $header = [
        'from' => '',
        'contentType' => 'Content-Type: text/html; charset="UTF-8"'."\r\n",
        'mime' => 'MIME-Version: 1.0'."\r\n",
    ];

    /**
     * @var string|null the email of the author of the mail
     */
    private ?string $from = null;

    /**
     * @var string the name of the author
     */
    private string $fromName;

    /**
     * @var string|null The addressee email
     */
    private ?string $to = null;

    /**
     * @var string|null The body of the email
     */
    private ?string $body = null;

    /**
     * @var string The subject of the email
     */
    private string $subject = 'No subject';

    /**
     * Sends the e-mail.
     */
    public function send(): bool
    {
        try {
            $this->validate();
            $this->header['from'] = 'From: "'.htmlspecialchars($this->fromName).'"<'.htmlspecialchars($this->from).'>'."\r\n";

            return mail($this->to, $this->subject, $this->body, join('', $this->header));
        } catch (\Exception $th) {
            return false;
        }
    }

    /**
     * Hydrates the addressee email and name.
     */
    public function setFrom(string $email, ?string $name = null): self
    {
        if ($this->isMail($email)) {
            $this->from = htmlspecialchars($email);
            $this->fromName = ($name) ? htmlspecialchars($name) : $email;
        }

        return $this;
    }

    /**
     * Hydrates the addressee email and name.
     */
    public function setTo(string $email): self
    {
        if ($this->isMail($email)) {
            $this->to = htmlspecialchars($email);
        }

        return $this;
    }

    /**
     * Sets the body of the email.
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Sets the subject of the email.
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Checks if an email is valid.
     */
    private function isMail(string $mail): bool
    {
        return (bool) preg_match("#^[a-z0-9-_.]+@[a-z0-9-_.]{2,}\.[a-z]{2,4}$#", $mail);
    }

    /**
     * Checks if the required values are not null.
     *
     * @throws \Exception
     */
    private function validate(): void
    {
        if (null === $this->to || null === $this->from || null === $this->body) {
            throw new \Exception('Invalid values Sets to $to, $from or $body.');
        }
    }
}
