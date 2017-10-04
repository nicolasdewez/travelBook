<?php

namespace App\Entity;

use App\Feedback\Subject;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="App\Repository\FeedbackRepository")
 */
class Feedback extends Timestampable
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotNull(message="user.unknown")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(length=50)
     *
     * @Assert\NotBlank
     * @Assert\Choice(callback={"App\Feedback\Subject", "getSubjects"}, strict=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(length=500)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="500")
     */
    private $comment;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $processed;

    public function __construct()
    {
        $this->processed = false;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Feedback
     */
    public function setUser(User $user): Feedback
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return Feedback
     */
    public function setSubject(string $subject): Feedback
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleSubject(): string
    {
        return Subject::getTitleBySubject($this->subject);
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return Feedback
     */
    public function setComment(string $comment): Feedback
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->processed;
    }

    /**
     * @param bool $processed
     *
     * @return Feedback
     */
    public function setProcessed(bool $processed): Feedback
    {
        $this->processed = $processed;

        return $this;
    }
}
