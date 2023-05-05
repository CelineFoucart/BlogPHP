<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

/**
 * BlogPost represents a blog post, persited in the database.
 */
final class BlogPost extends AbstractEntity
{
    /**
     * @var string|null the title of the post
     */
    private ?string $title = null;

    /**
     * @var string|null the slug of the post
     */
    private ?string $slug = null;

    /**
     * @var string|null a short description of the post
     */
    private ?string $description = null;

    /**
     * @var string|null the body of the post
     */
    private ?string $content = null;

    /**
     * @var DateTime|null The publication date of the post
     */
    private ?DateTime $createdAt = null;

    /**
     * @var DateTime|null The last updated date of the post
     */
    private ?DateTime $updatedAt = null;

    /**
     * @var BlogUser|null The author of the post
     */
    private ?BlogUser $author = null;

    /**
     * Gets the value of title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of slug.
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Gets the value of description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

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
        } elseif (is_string($updatedAt)) {
            $this->updatedAt = new DateTime($updatedAt);
        }

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
}
