<?php

declare(strict_types=1);

namespace App\Service;

class Mailer
{
    private array $header = [
        "from" => "",
        "contentType" => "Content-Type: text/html; charset=\"UTF-8\"" . "\r\n",
        "mime" => "MIME-Version: 1.0" . "\r\n",
    ];

    private ?string $from = null;

    private string $fromName;
    
    private ?string $to = null;
    
    private ?string $body = null;

    private string $subject = "No subject";

    /**
     * Send the e-mail.
     */
    public function send(): bool
    {
        try {
            $this->validate();
            $this->header['from'] = "From: \"" . htmlspecialchars($this->fromName) . "\"<" . htmlspecialchars($this->from) . ">" . "\r\n";

            return mail($this->to, $this->subject, $this->body, join("", $this->header)); 
        } catch (\Exception $th) {
            return false;
        }
    }  
    
    /**
     * Hydrate the addressee email and name.
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
     * Hydrate the addressee email and name.
     */
    public function setTo(string $email): self
    {
        if ($this->isMail($email)) {
            $this->to = htmlspecialchars($email);
        }

        return $this;
    }  
    
    /**
     * Set the body of the email.
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }


    /**
     * Set the subject of the email.
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    private function isMail(string $mail): bool
    {
        return (bool) preg_match("#^[a-z0-9-_.]+@[a-z0-9-_.]{2,}\.[a-z]{2,4}$#", $mail);
    }

    private function validate(): void
    {
        if (null === $this->to || null === $this->from || null === $this->body) {
            throw new \Exception('Invalid values set to $to, $from or $body.');
        }
    }
}