<?php

declare(strict_types=1);

namespace App\Service;

class Pagination
{
    private ?int $current;

    private array $pages = [];

    private ?string $previousLink;

    private ?string $nextLink;

    private ?int $numberItems;

    private ?string $perPage;

    private array $elements = [];

    /**
     * Get the value of current.
     */
    public function getCurrent(): ?int
    {
        return $this->current;
    }

    /**
     * Set the value of current.
     */
    public function setCurrent(?int $current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get the value of pages.
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Set the value of pages.
     */
    public function setPages(array $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get the value of previousLink.
     *
     * @return ?string
     */
    public function getPreviousLink(): ?string
    {
        return $this->previousLink;
    }

    /**
     * Set the value of previousLink.
     *
     * @param ?string $previousLink
     */
    public function setPreviousLink(?string $previousLink): self
    {
        $this->previousLink = $previousLink;

        return $this;
    }

    /**
     * Get the value of nextLink.
     *
     * @return ?string
     */
    public function getNextLink(): ?string
    {
        return $this->nextLink;
    }

    /**
     * Set the value of nextLink.
     *
     * @param ?string $nextLink
     */
    public function setNextLink(?string $nextLink): self
    {
        $this->nextLink = $nextLink;

        return $this;
    }

    /**
     * Get the value of numberItems.
     *
     * @return ?int
     */
    public function getNumberItems(): ?int
    {
        return $this->numberItems;
    }

    /**
     * Set the value of numberItems.
     *
     * @param ?int $numberItems
     */
    public function setNumberItems(?int $numberItems): self
    {
        $this->numberItems = $numberItems;

        return $this;
    }

    /**
     * Get the value of perPage.
     *
     * @return ?string
     */
    public function getPerPage(): ?string
    {
        return $this->perPage;
    }

    /**
     * Set the value of perPage.
     *
     * @param ?string $perPage
     */
    public function setPerPage(?string $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the value of elements.
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Set the value of elements.
     */
    public function setElements(array $elements): self
    {
        $this->elements = $elements;

        return $this;
    }

    public function count(): int
    {
        return count($this->elements);
    }
}
