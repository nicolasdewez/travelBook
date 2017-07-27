<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="places", indexes={@ORM\Index(name="places_title", columns={"title"})})
 * @ORM\Entity
 */
class Place
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=30, unique=true)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=12, precision=18)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=12, precision=18)
     */
    private $longitude;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param string $title
     *
     * @return Place
     */
    public function setTitle(string $title): Place
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param float $latitude
     *
     * @return Place
     */
    public function setLatitude(float $latitude): Place
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     *
     * @return Place
     */
    public function setLongitude(float $longitude): Place
    {
        $this->longitude = $longitude;

        return $this;
    }
}
