<?php

namespace App\Model;

class FilterPicture
{
    /** @var string */
    private $title;

    /** @var string */
    private $username;

    /** @var string */
    private $state;

    /** @var string */
    private $sort;

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return FilterPicture
     */
    public function setTitle(?string $title): FilterPicture
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return FilterPicture
     */
    public function setUsername(?string $username): FilterPicture
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return FilterPicture
     */
    public function setState(?string $state): FilterPicture
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     *
     * @return FilterPicture
     */
    public function setSort(?string $sort): FilterPicture
    {
        $this->sort = $sort;

        return $this;
    }
}
