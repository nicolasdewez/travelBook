<?php

namespace App\Tests\Form\DataTransformer;

use App\Entity\Place;
use App\Form\DataTransformer\PlaceToNumberTransformer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PlaceToNumberTransformerTest extends TestCase
{
    public function testTransformWithPlaceNull()
    {
        $transformer = new PlaceToNumberTransformer($this->createMock(EntityManagerInterface::class));

        $this->assertSame('', $transformer->transform(null));
    }

    public function testTransform()
    {
        $transformer = new PlaceToNumberTransformer($this->createMock(EntityManagerInterface::class));

        $place = new Place();
        $class = new \ReflectionClass($place);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($place, 10);

        $this->assertSame(10, $transformer->transform($place));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testReverseTransformNotFound()
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn(null)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Place::class)
            ->willReturn($repository)
        ;

        $transformer =  new PlaceToNumberTransformer($manager);
        $transformer->reverseTransform(10);
    }

    public function testReverseTransform()
    {
        $place = new Place();

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn($place)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Place::class)
            ->willReturn($repository)
        ;

        $transformer =  new PlaceToNumberTransformer($manager);
        $this->assertSame($place, $transformer->reverseTransform(10));
    }
}
