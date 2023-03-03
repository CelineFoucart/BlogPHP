<?php

namespace App\Entity;

use DateTime;

class Comment extends AbstractEntity
{
    private ?string $pseudo = null;

    private ?string $content = null;

    private ?DateTime $createdAt = null;

    private ?Post $post = null;

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
     *
     * @return ?Post
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * Set the value of post.
     *
     * @param ?Post $post
     */
    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
