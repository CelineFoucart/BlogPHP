<?php

namespace App\Entity;

use DateTime;

class Post extends AbstractEntity
{
    private ?string $title = null;

    private ?string $description = null;

    private ?string $content = null;

    private ?DateTime $createdAt = null;

    private ?DateTime $updatedAt = null;

    private ?User $author = null;

    /**
     * Get the value of title.
     *
     * @return ?string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title.
     *
     * @param ?string $title
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of description.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description.
     *
     * @param ?string $description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of content.
     *
     * @return ?string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set the value of content.
     *
     * @param ?string $content
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of createdAt.
     *
     * @return ?DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt.
     *
     * @param ?DateTime $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt.
     *
     * @return ?DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt.
     *
     * @param ?DateTime $updatedAt
     */
    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of author.
     *
     * @return ?User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set the value of author.
     *
     * @param ?User $author
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
