<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="travels")
 * @ORM\Entity
 */
class Travel extends Timestampable
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
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Assert\Type("string")
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank
     * @Assert\Date
     * @Assert\LessThanOrEqual("today")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank
     * @Assert\Date
     * @Assert\LessThanOrEqual("today")
     */
    private $endDate;

    /**
     * @var Place
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", cascade={"all"})
     *
     * @Assert\NotNull
     */
    private $place;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="travels")
     *
     * @Assert\NotNull
     */
    private $user;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="travel", orphanRemoval=true)
     */
    private $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $title
     *
     * @return Travel
     */
    public function setTitle(string $title): Travel
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
     * @return \DateTime
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     *
     * @return Travel
     */
    public function setStartDate(\DateTime $startDate): Travel
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     *
     * @return Travel
     */
    public function setEndDate(\DateTime $endDate): Travel
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Place
     */
    public function getPlace(): ?Place
    {
        return $this->place;
    }

    /**
     * @param Place $place
     *
     * @return Travel
     */
    public function setPlace(Place $place): Travel
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Travel
     */
    public function setUser(User $user): Travel
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    /**
     * @param Collection $pictures
     *
     * @return Travel
     */
    public function setPictures(Collection $pictures): Travel
    {
        $this->pictures = $pictures;

        return $this;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param Travel                    $payload
     *
     * @Assert\Callback
     */
    public function startDateBeforeEndDate(ExecutionContextInterface $context, $payload)
    {
        if ($this->startDate->getTimestamp() <= $this->endDate->getTimestamp()) {
            return;
        }

        $context
            ->buildViolation('dates.start_before_end')
            ->addViolation()
        ;
    }
}
