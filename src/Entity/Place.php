<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="places",
 *     indexes={
 *         @ORM\Index(name="places_title_locale", columns={"title", "locale"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="places_unique", columns={"title", "locale"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
 *
 * @UniqueEntity(fields={"title", "locale"})
 */
class Place extends Timestampable implements SimpleEntityDenormalizableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"entity_place_get"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=30)
     *
     * @Serializer\Groups({"entity_place_get"})
     *
     * @Assert\NotBlank
     * @Assert\Length(max=30)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(length=3)
     *
     * @Serializer\Groups({"entity_place_get"})
     *
     * @Assert\NotBlank
     * @Assert\Choice(callback={"App\Translation\Locale", "getLocales"}, strict=true)
     */
    private $locale;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=12, precision=18)
     *
     * @Serializer\Groups({"entity_place_get"})
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=12, precision=18)
     *
     * @Serializer\Groups({"entity_place_get"})
     */
    private $longitude;

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
     * @return string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Place
     */
    public function setLocale(string $locale): Place
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
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

    /**
     * @param ExecutionContextInterface $context
     * @param Place                     $payload
     *
     * @Assert\Callback
     */
    public function locationIsRequired(ExecutionContextInterface $context, $payload)
    {
        if (null !== $this->latitude && null !== $this->longitude) {
            return;
        }

        $context
            ->buildViolation('place.location_required')
            ->addViolation()
        ;
    }
}
