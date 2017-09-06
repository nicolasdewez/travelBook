<?php

namespace App\Model;

class ResultPlace
{
    /** @var string */
    private $title;

    /** @var string */
    private $locale;

    /** @var float */
    private $latitude;

    /** @var float */
    private $longitude;

    /** @var string */
    private $linkShow;

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
     * @return ResultPlace
     */
    public function setTitle(string $title): ResultPlace
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return ResultPlace
     */
    public function setLocale(string $locale): ResultPlace
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     *
     * @return ResultPlace
     */
    public function setLatitude(float $latitude): ResultPlace
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     *
     * @return ResultPlace
     */
    public function setLongitude(float $longitude): ResultPlace
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkShow(): string
    {
        return $this->linkShow;
    }

    /**
     * @param string $linkShow
     *
     * @return ResultPlace
     */
    public function setLinkShow(string $linkShow): ResultPlace
    {
        $this->linkShow = $linkShow;

        return $this;
    }
}
