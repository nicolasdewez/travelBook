<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Workflow\CheckPictureDefinitionWorkflow;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="pictures")
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 *
 * @ApiResource(
 *     iri="http://schema.org/Picture",
 *     collectionOperations={"get"={"method"="GET"}},
 *     itemOperations={"get"={"method"="GET"}},
 *     attributes={
 *         "normalization_context"={"groups"={"api_picture_get"}}
 *     }
 * )
 */
class Picture extends Timestampable implements SimpleEntityDenormalizableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"event_analyze_picture", "api_picture_get"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Serializer\Groups({"api_picture_get"})
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     *
     * @Serializer\Groups({"api_picture_get"})
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     */
    private $checkState;

    /**
     * @var Place
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", cascade={"all"})
     *
     * @Serializer\Groups({"api_picture_get"})
     *
     * @ApiSubresource
     */
    private $place;

    /**
     * @var Travel
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Travel", inversedBy="pictures", cascade={"all"})
     */
    private $travel;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\InvalidationPicture", mappedBy="picture", orphanRemoval=true)
     */
    private $invalidation;

    /**
     * @var UploadedFile
     *
     * @Assert\NotBlank
     * @Assert\Image
     */
    private $file;

    /**
     * @var string
     *
     * @Serializer\Groups({"api_picture_get"})
     */
    private $content;

    public function __construct()
    {
        $this->invalidation = new ArrayCollection();
        $this->checkState = CheckPictureDefinitionWorkflow::PLACE_UPLOADED;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Picture
     */
    public function setName(string $name): Picture
    {
        $this->name = $name;

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
     * @return string
     */
    public function getCheckState(): string
    {
        return $this->checkState;
    }

    /**
     * @param string $checkState
     *
     * @return Picture
     */
    public function setCheckState(string $checkState): Picture
    {
        $this->checkState = $checkState;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleCheckState(): string
    {
        return CheckPictureDefinitionWorkflow::getTitleByPlace($this->checkState);
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return CheckPictureDefinitionWorkflow::PLACE_VALIDATED === $this->checkState;
    }

    /**
     * @return bool
     */
    public function isValidationInProgress(): bool
    {
        return in_array($this->checkState, [
            CheckPictureDefinitionWorkflow::PLACE_UPLOADED,
            CheckPictureDefinitionWorkflow::PLACE_HEALTHY,
        ]);
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
     * @return Picture
     */
    public function setPlace(Place $place): Picture
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return Travel
     */
    public function getTravel(): ?Travel
    {
        return $this->travel;
    }

    /**
     * @param Travel $travel
     *
     * @return Picture
     */
    public function setTravel(Travel $travel): Picture
    {
        $this->travel = $travel;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getInvalidation(): Collection
    {
        return $this->invalidation;
    }

    /**
     * @param Collection $invalidation
     *
     * @return Picture
     */
    public function setInvalidation(Collection $invalidation): Picture
    {
        $this->invalidation = $invalidation;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return Picture
     */
    public function setFile(UploadedFile $file): Picture
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Picture
     */
    public function setContent(string $content): Picture
    {
        $this->content = $content;

        return $this;
    }
}
