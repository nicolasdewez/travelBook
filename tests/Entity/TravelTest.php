<?php

namespace App\Tests\Entity;

use App\Entity\Travel;
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
}
