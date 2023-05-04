<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

/**
 * BlogPost represents a blog post, persited in the database.
 */
final class BlogPost extends AbstractEntity
{
    private ?string $title = null;

    private ?string $slug = null;

    private ?string $description = null;

    private ?string $content = null;

    private ?DateTime $createdAt = null;

    private ?DateTime $updatedAt = null;

    private ?BlogUser $author = null;

    /**
     * Gets the value of title.
     *
     * @return ?string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param ?string $title
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of slug.
     *
     * @return ?string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param ?string $slug
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param ?string $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of content.
     *
     * @return ?string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     *
     * @param ?string $content
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the value of createdAt.
     *
     * @return ?DateTime
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
        } elseif (is_string($createdAt)) {
            $this->createdAt = new DateTime($createdAt);
        }

        return $this;
    }

    /**
     * Gets the value of updatedAt.
     *
     * @return ?DateTime
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
        } elseif (is_string($updatedAt)) {
            $this->updatedAt = new DateTime($updatedAt);
        }

        return $this;
    }

    /**
     * Gets the value of author.
     *
     * @return ?BlogUser
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
     *
     * @param ?BlogUser $author
     */
    public function setAuthor(?BlogUser $author): self
    {
        $this->author = $author;

        return $this;
    }
}