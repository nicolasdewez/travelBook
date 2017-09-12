<?php

namespace App\Form\DataTransformer;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PlaceToNumberTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (place) to a string (number).
     *
     * @param Place|null $place
     *
     * @return string
     */
    public function transform($place)
    {
        if (null === $place) {
            return '';
        }

        return $place->getId();
    }

    /**
     * Transforms a string (number) to an object (place).
     *
     * @param string $placeNumber
     *
     * @return Place|null
     *
     * @throws TransformationFailedException if object (place) is not found
     */
    public function reverseTransform($placeNumber)
    {
        $place = $this->manager->getRepository(Place::class)->find($placeNumber);

        if (null === $place) {
            throw new TransformationFailedException(sprintf(
                'A place with number "%s" does not exist!',
                $placeNumber
            ));
        }

        return $place;
    }
}
