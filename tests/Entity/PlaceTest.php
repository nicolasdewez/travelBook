<?php

namespace App\Tests\Entity;

use App\Entity\Place;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class PlaceTest extends TestCase
{
    public function testLocationIsRequiredNoViolation()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->never())
            ->method('buildViolation')
        ;

        $place = (new Place())
            ->setLatitude(12)
            ->setLongitude(13)
        ;

        $place->locationIsRequired($context, null);
    }

    public function testLocationIsRequiredBuildViolation()
    {
        $constraint = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint
            ->expects($this->exactly(3))
            ->method('addViolation')
            ->withAnyParameters()
            ->willReturn(true)
        ;

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->exactly(3))
            ->method('buildViolation')
            ->with('place.location_required')
            ->willReturn($constraint)
        ;

        $place = new Place();
        $place->locationIsRequired($context, null);

        $place->setLongitude(12);
        $place->locationIsRequired($context, null);

        $place = (new Place())->setLatitude(12)
        ;
        $place->locationIsRequired($context, null);
    }
}
