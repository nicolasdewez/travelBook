<?php

namespace App\Serializer;

use App\Entity\SimpleEntityDenormalizableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SimpleEntityDenormalizer implements DenormalizerInterface
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
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return $this->manager->getRepository($class)->find($data['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $interfaces = class_implements($type);

        return in_array(SimpleEntityDenormalizableInterface::class, $interfaces) && isset($data['id']);
    }
}
