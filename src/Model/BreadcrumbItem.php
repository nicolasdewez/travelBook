<?php

namespace App\Model;

class BreadcrumbItem
{
    /** @var string */
    private $title;

    /** @var string */
    private $url;

    /** @var bool */
    private $active;

    /**
     * @param string $title
     * @param string $url
     * @param bool   $active
     */
    public function __construct(string $title, string $url = null, bool $active = false)
    {
        $this->title = $title;
        $this->url = $url;
        $this->active = $active;
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
     * @return BreadcrumbItem
     */
    public function setTitle(string $title): BreadcrumbItem
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return BreadcrumbItem
     */
    public function setUrl(string $url): BreadcrumbItem
    {
        $this->url = $url;

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
     * @return BreadcrumbItem
     */
    public function setActive(bool $active): BreadcrumbItem
    {
        $this->active = $active;

        return $this;
    }
}
