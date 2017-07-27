<?php

namespace App\Model;

class MenuItem
{
    /** @var string */
    private $title;

    /** @var string */
    private $route;

    /** @var bool */
    private $active;

    /** @var MenuItem[] */
    private $items;

    /**
     * @param string     $title
     * @param string     $route
     * @param bool       $active
     * @param MenuItem[] $items
     */
    public function __construct(string $title, string $route = null, bool $active = false, array $items = [])
    {
        $this->title = $title;
        $this->route = $route;
        $this->active = $active;
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return MenuItem
     */
    public function setTitle(string $title): MenuItem
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param string $route
     *
     * @return MenuItem
     */
    public function setRoute(string $route): MenuItem
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return MenuItem
     */
    public function setActive(bool $active): MenuItem
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param MenuItem[] $items
     *
     * @return MenuItem
     */
    public function setItems(array $items): MenuItem
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return MenuItem
     */
    public function setActiveFromItems(): MenuItem
    {
        foreach ($this->items as $item) {
            if ($item->isActive()) {
                $this->active = true;

                return $this;
            }
        }

        $this->active = false;

        return $this;
    }
}
