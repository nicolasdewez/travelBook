<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pictures")
 * @ORM\Entity
 */
class Picture
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
     * @ORM\Column
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var Place
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Travel", inversedBy="pictures", cascade={"all"})
     */
    private $travel;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

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
     * @return Picture
     */
    public function setTitle(string $title): Picture
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return Picture
     */
    public function setDate(\DateTime $date): Picture
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Place
     */
    public function getTravel(): ?Place
    {
        return $this->travel;
    }

    /**
     * @param Place $travel
     *
     * @return Picture
     */
    public function setTravel(Place $travel): Picture
    {
        $this->travel = $travel;

        return $this;
    }
}
