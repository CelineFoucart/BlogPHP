<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

final class Comment extends AbstractEntity
{
    private ?string $pseudo = null;

    private ?string $content = null;

    private ?DateTime $createdAt = null;

    private ?BlogPost $post = null;

    public function __construct()
    {
        $this->post = new BlogPost();
    }

    /**
     * Get the value of pseudo.
     *
     * @return ?string
     */
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    /**
     * Set the value of pseudo.
     *
     * @param ?string $pseudo
     */
    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

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
     * Get the value of post.
     */
    public function getPost(): ?BlogPost
    {
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
}
