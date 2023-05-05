<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

/**
 * Comment represents a comment, persited in the database.
 */
final class Comment extends AbstractEntity
{
    /**
     * @var BlogUser|null The comment author
     */
    private ?BlogUser $author = null;

    /**
     * @var string|null The message of the comment
     */
    private ?string $content = null;

    /**
     * @var DateTime|null The publication date of the comment
     */
    private ?DateTime $createdAt = null;

    /**
     * @var DateTime|null The last updated date of the comment
     */
    private ?DateTime $updatedAt = null;

    /**
     * @var BlogPost|null The blog post the message comments
     */
    private ?BlogPost $post = null;

    /**
     * @var bool true if the admin has validated the comment
     */
    private bool $isValidated = false;

    /**
     * Gets the value of content.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the value of createdAt.
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Sets the value of createdAt.
     *
     * @param DateTime|string|null $createdAt
     */
    public function setCreatedAt($createdAt): self
    {
        if ($createdAt instanceof DateTime) {
            $this->createdAt = $createdAt;
        } elseif (is_string($createdAt) === true) {
            $this->createdAt = new DateTime($createdAt);
        }

        return $this;
    }

    /**
     * Gets the value of updatedAt.
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Sets the value of updatedAt.
     *
     * @param DateTime|string|null $updatedAt
     */
    public function setUpdatedAt($updatedAt): self
    {
        if ($updatedAt instanceof DateTime) {
            $this->updatedAt = $updatedAt;
        } elseif (is_string($updatedAt) === true) {
            $this->updatedAt = new DateTime($updatedAt);
        }

        return $this;
    }

    /**
     * Gets the value of post.
     */
    public function getPost(): ?BlogPost
    {
        if (!$this->post) {
            $this->post = new BlogPost();
        }

        return $this->post;
    }

    /**
     * Sets the value of post.
     */
    public function setPost(?BlogPost $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Gets the value of author.
     */
    public function getAuthor(): ?BlogUser
    {
        if (!$this->author) {
            $this->author = new BlogUser();
        }

        return $this->author;
    }

    /**
     * Sets the value of author.
     */
    public function setAuthor(?BlogUser $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Gets the value of isValidated.
     */
    public function getIsValidated(): bool
    {
        return $this->isValidated;
    }

    /**
     * Sets the value of isValidated.
     */
    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
}
