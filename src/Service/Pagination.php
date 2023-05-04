<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Pagination represents a pagination of value.
 */
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
     * Gets the value of current.
     */
    public function getCurrent(): ?int
    {
        return $this->current;
    }

    /**
     * Sets the value of current.
     */
    public function setCurrent(?int $current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Gets the value of pages.
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Sets the value of pages.
     */
    public function setPages(array $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Gets the value of previousLink.
     *
     * @return ?string
     */
    public function getPreviousLink(): ?string
    {
        return $this->previousLink;
    }

    /**
     * Sets the value of previousLink.
     *
     * @param ?string $previousLink
     */
    public function setPreviousLink(?string $previousLink): self
    {
        $this->previousLink = $previousLink;

        return $this;
    }

    /**
     * Gets the value of nextLink.
     *
     * @return ?string
     */
    public function getNextLink(): ?string
    {
        return $this->nextLink;
    }

    /**
     * Sets the value of nextLink.
     *
     * @param ?string $nextLink
     */
    public function setNextLink(?string $nextLink): self
    {
        $this->nextLink = $nextLink;

        return $this;
    }

    /**
     * Gets the value of numberItems.
     *
     * @return ?int
     */
    public function getNumberItems(): ?int
    {
        return $this->numberItems;
    }

    /**
     * Sets the value of numberItems.
     *
     * @param ?int $numberItems
     */
    public function setNumberItems(?int $numberItems): self
    {
        $this->numberItems = $numberItems;

        return $this;
    }

    /**
     * Gets the value of perPage.
     *
     * @return ?string
     */
    public function getPerPage(): ?string
    {
        return $this->perPage;
    }

    /**
     * Sets the value of perPage.
     *
     * @param ?string $perPage
     */
    public function setPerPage(?string $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Gets the value of elements.
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Sets the value of elements.
     */
    public function setElements(array $elements): self
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * Counts the elements in the current page of pagination.
     */
    public function count(): int
    {
        return count($this->elements);
    }
}
