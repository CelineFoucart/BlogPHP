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
     * Get the value of current
     *
     * @return ?int
     */
    public function getCurrent(): ?int
    {
        return $this->current;
    }

    /**
     * Set the value of current
     *
     * @param ?int $current
     *
     * @return self
     */
    public function setCurrent(?int $current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get the value of pages
     *
     * @return array
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Set the value of pages
     *
     * @param array $pages
     *
     * @return self
     */
    public function setPages(array $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get the value of previousLink
     *
     * @return ?string
     */
    public function getPreviousLink(): ?string
    {
        return $this->previousLink;
    }

    /**
     * Set the value of previousLink
     *
     * @param ?string $previousLink
     *
     * @return self
     */
    public function setPreviousLink(?string $previousLink): self
    {
        $this->previousLink = $previousLink;

        return $this;
    }

    /**
     * Get the value of nextLink
     *
     * @return ?string
     */
    public function getNextLink(): ?string
    {
        return $this->nextLink;
    }

    /**
     * Set the value of nextLink
     *
     * @param ?string $nextLink
     *
     * @return self
     */
    public function setNextLink(?string $nextLink): self
    {
        $this->nextLink = $nextLink;

        return $this;
    }

    /**
     * Get the value of numberItems
     *
     * @return ?int
     */
    public function getNumberItems(): ?int
    {
        return $this->numberItems;
    }

    /**
     * Set the value of numberItems
     *
     * @param ?int $numberItems
     *
     * @return self
     */
    public function setNumberItems(?int $numberItems): self
    {
        $this->numberItems = $numberItems;

        return $this;
    }

    /**
     * Get the value of perPage
     *
     * @return ?string
     */
    public function getPerPage(): ?string
    {
        return $this->perPage;
    }

    /**
     * Set the value of perPage
     *
     * @param ?string $perPage
     *
     * @return self
     */
    public function setPerPage(?string $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the value of elements
     *
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Set the value of elements
     *
     * @param array $elements
     *
     * @return self
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