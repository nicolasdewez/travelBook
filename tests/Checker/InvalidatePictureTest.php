<?php

namespace App\Tests\Checker;

use App\Checker\ValidatePicture;
use App\Entity\Picture;
use App\Workflow\CheckPictureWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ValidatePictureTest extends TestCase
{
    public function testExecuteWorkflowInvalid()
    {
        $picture = $this->getPicture();

        $workflow = $this->createMock(CheckPictureWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyValidation')
            ->with($picture)
            ->willReturn(false)
        ;

        $workflow
            ->expects($this->never())
            ->method('applyInvalidation')
        ;

        $validatePicture = new ValidatePicture(
            $this->createMock(EntityManagerInterface::class),
            $workflow,
            new NullLogger()
        );

        $validatePicture->execute($picture);
    }

    public function testExecute()
    {
        $picture = $this->getPicture();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $workflow = $this->createMock(CheckPictureWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyValidation')
            ->with($picture)
            ->willReturn(true)
        ;

        $workflow
            ->expects($this->once())
            ->method('applyValidation')
            ->with($picture);
        ;

        $validatePicture = new ValidatePicture(
            $manager,
            $workflow,
            new NullLogger()
        );

        $validatePicture->execute($picture);
    }

    /**
     * @return Picture
     */
    private function getPicture(): Picture
    {
        $picture = new Picture();
        $class = new \ReflectionClass($picture);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($picture, 1);

        return $picture;
    }
}
