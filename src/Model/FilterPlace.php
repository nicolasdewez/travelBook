<?php

namespace App\Model;

class FilterPlace
{
    /** @var string */
    private $title;

    /** @var string */
    private $locale;

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
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return FilterPlace
     */
    public function setLocale(?string $locale): FilterPlace
    {
        $this->locale = $locale;

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
