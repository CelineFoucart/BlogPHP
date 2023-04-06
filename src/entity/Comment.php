<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

final class Comment extends AbstractEntity
{
    private ?BlogUser $author = null;

    private ?string $content = null;

    private ?DateTime $createdAt = null;

    private ?DateTime $updatedAt = null;

    private ?BlogPost $post = null;

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
     * Get the value of post.
     */
    public function getPost(): ?BlogPost
    {
        if (!$this->post) {
            $this->post = new BlogPost();
        }

        return $this->post;
    }

    /**
     * Set the value of post.
     */
    public function setPost(?BlogPost $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get the value of author.
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
     * Set the value of author.
     *
     * @param ?BlogUser $author
     */
    public function setAuthor(?BlogUser $author): self
    {
        $this->author = $author;

        return $this;
    }
}
