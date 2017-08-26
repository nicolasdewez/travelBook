<?php

namespace App\Entity;

use App\Checker\InvalidatePictureReason;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="invalidation_pictures")
 * @ORM\Entity
 */
class InvalidationPicture
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"event_invalid_picture"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $comment;

    /**
     * @var Picture
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Picture", inversedBy="invalidation", cascade={"all"})
     */
    private $picture;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $reason
     *
     * @return InvalidationPicture
     */
    public function setReason(string $reason): InvalidationPicture
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string $comment
     *
     * @return InvalidationPicture
     */
    public function setComment(string $comment): InvalidationPicture
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return Picture
     */
    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    /**
     * @param Picture $picture
     *
     * @return InvalidationPicture
     */
    public function setPicture(Picture $picture): InvalidationPicture
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        $user = $this->picture->getTravel()->getUser();

        return sprintf('%s (%s %s)', $user->getUsername(), $user->getFirstname(), $user->getLastname());
    }

    /**
     * @return string
     */
    public function getTravel(): string
    {
        $travel = $this->picture->getTravel();

        return sprintf(
            '%s (%s %s-%s)',
            $travel->getTitle(),
            $travel->getPlace()->getTitle(),
            $travel->getStart()->format('d/m/Y'),
            $travel->getEnd()->format('d/m/Y')
        );
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->picture->getPlace()->getTitle();
    }

    /**
     * @return string
     */
    public function getTitleReason(): string
    {
        return InvalidatePictureReason::getTitle($this->reason);
    }

    /**
     * @param ExecutionContextInterface $context
     * @param User                      $payload
     *
     * @Assert\Callback
     */
    public function commentIsRequiredWithReasonOthers(ExecutionContextInterface $context, $payload)
    {
        if (InvalidatePictureReason::OTHERS !== $this->reason) {
            return;
        }

        if (null !== $this->comment) {
            return;
        }

        $context
            ->buildViolation('invalidation_picture.comment_required')
            ->atPath('comment')
            ->addViolation()
        ;
    }
}
