<?php

namespace App\Model;

class FilterPlace
{
    /** @var string */
    private $title;

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
     * @return FilterPlace
     */
    public function setTitle(?string $title): FilterPlace
    {
        $this->title = $title;

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
     * @return FilterPlace
     */
    public function setSort(?string $sort): FilterPlace
    {
        $this->sort = $sort;

        return $this;
    }
}
