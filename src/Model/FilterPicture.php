<?php

namespace App\Model;

class FilterPicture
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
