<?php

namespace App\Tests\Entity;

use App\Entity\Picture;
use App\Entity\Travel;
use App\Workflow\CheckPictureDefinitionWorkflow;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class TravelTest extends TestCase
{
    public function testStartDateBeforeEndDateNoViolation()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->never())
            ->method('buildViolation')
        ;

        $travel = (new Travel())
            ->setStartDate(new \DateTime('2017-01-01'))
            ->setEndDate(new \DateTime('2017-01-01'))
        ;

        $travel->startDateBeforeEndDate($context, null);

        $travel->setEndDate(new \DateTime('2017-01-02'));
        $travel->startDateBeforeEndDate($context, null);
    }

    public function testLocationIsRequiredBuildViolation()
    {
        $constraint = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint
            ->expects($this->once())
            ->method('addViolation')
            ->withAnyParameters()
            ->willReturn(true)
        ;

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('dates.start_before_end')
            ->willReturn($constraint)
        ;

        $travel = (new Travel())
            ->setStartDate(new \DateTime('2017-01-02'))
            ->setEndDate(new \DateTime('2017-01-01'))
        ;

        $travel->startDateBeforeEndDate($context, null);
    }

    public function testGetPicturesValidated()
    {
        $picture1 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_INVALID);
        $picture2 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_VALIDATED);
        $picture3 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_HEALTHY);

        $pictures = [$picture1, $picture2, $picture3];

        $travel = (new Travel())->setPictures(new ArrayCollection($pictures));

        $this->assertSame([$picture2], $travel->getPicturesValidated());
    }

    public function testCountPicturesValidated()
    {
        $picture1 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_INVALID);
        $picture2 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_VALIDATED);
        $picture3 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_HEALTHY);

        $pictures = [$picture1, $picture2, $picture3];

        $travel = (new Travel())->setPictures(new ArrayCollection($pictures));

        $this->assertSame(1, $travel->countPicturesValidated());
    }

    public function testCountPicturesValidationInProgress()
    {
        $picture1 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_INVALID);
        $picture2 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_UPLOADED);
        $picture3 = (new Picture())->setCheckState(CheckPictureDefinitionWorkflow::PLACE_HEALTHY);

        $pictures = [$picture1, $picture2, $picture3];

        $travel = (new Travel())->setPictures(new ArrayCollection($pictures));

        $this->assertSame(2, $travel->countPicturesValidationInProgress());
    }
}
