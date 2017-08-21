<?php

namespace App\Tests\Checker;

use App\Checker\InvalidatePicture;
use App\Entity\InvalidationPicture;
use App\Entity\Picture;
use App\Producer\MailInvalidPictureProducer;
use App\Workflow\CheckPictureWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class InvalidatePictureTest extends TestCase
{
    public function testExecuteWorkflowInvalid()
    {
        $picture = $this->getPicture();
        $invalidationPicture = (new InvalidationPicture())->setPicture($picture);

        $workflow = $this->createMock(CheckPictureWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyInvalidation')
            ->with($picture)
            ->willReturn(false)
        ;

        $workflow
            ->expects($this->never())
            ->method('applyInvalidation')
        ;

        $invalidatePicture = new InvalidatePicture(
            $this->createMock(EntityManagerInterface::class),
            $workflow,
            $this->createMock(MailInvalidPictureProducer::class),
            new NullLogger()
        );

        $invalidatePicture->execute($invalidationPicture);
    }

    public function testExecute()
    {
        $picture = $this->getPicture();
        $invalidationPicture = (new InvalidationPicture())->setPicture($picture);

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('persist')
            ->with($invalidationPicture)
        ;

        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $workflow = $this->createMock(CheckPictureWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyInvalidation')
            ->with($picture)
            ->willReturn(true)
        ;

        $workflow
            ->expects($this->once())
            ->method('applyInvalidation')
            ->with($picture);
        ;

        $producer = $this->createMock(MailInvalidPictureProducer::class);
        $producer
            ->expects($this->once())
            ->method('execute')
            ->with($invalidationPicture)
        ;
        
        $invalidatePicture = new InvalidatePicture(
            $manager,
            $workflow,
            $producer,
            new NullLogger()
        );

        $invalidatePicture->execute($invalidationPicture);
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
