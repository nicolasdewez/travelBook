<?php

namespace App\Tests\Checker;

use App\Checker\PictureChecker;
use App\Checker\VirusChecker;
use App\Entity\Picture;
use App\Mailer\PictureIsVirusMailer;
use App\Workflow\CheckPictureWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class PictureCheckerTest extends TestCase
{
    public function testExecuteWorkflowInvalid()
    {
        $picture = $this->getPicture();

        $workflow = $this->createMock(CheckPictureWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyAnalyze')
            ->with($picture)
            ->willReturn(false)
        ;

        $pictureChecker = new PictureChecker(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(VirusChecker::class),
            $this->createMock(PictureIsVirusMailer::class),
            $workflow,
            new NullLogger(),
            ''
        );

        $pictureChecker->execute($picture);
    }

    public function testExecuteAnalyzeOk()
    {
        $picture = $this->getPicture();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $checker = $this->createMock(VirusChecker::class);
        $checker
            ->expects($this->once())
            ->method('execute')
            ->with(new \SplFileInfo('/path/to/data/1'))
            ->willReturn(true)
        ;

        $workflow = $this->createMock(CheckPictureWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyAnalyze')
            ->with($picture)
            ->willReturn(true)
        ;

        $workflow
            ->expects($this->once())
            ->method('applyAnalyzeOk')
            ->with($picture)
        ;

        $workflow
            ->expects($this->never())
            ->method('applyAnalyzeKo')
        ;

        $pictureChecker = new PictureChecker(
            $manager,
            $checker,
            $this->createMock(PictureIsVirusMailer::class),
            $workflow,
            new NullLogger(),
            '/path/to/data'
        );

        $pictureChecker->execute($picture);
    }

    public function testExecuteAnalyzeKo()
    {
        $picture = $this->getPicture();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $checker = $this->createMock(VirusChecker::class);
        $checker
            ->expects($this->once())
            ->method('execute')
            ->with(new \SplFileInfo('/path/to/data/1'))
            ->willReturn(false)
        ;

        $mailer = $this->createMock(PictureIsVirusMailer::class);
        $mailer
            ->expects($this->once())
            ->method('execute')
            ->with($picture)
        ;

        $workflow = $this->createMock(CheckPictureWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyAnalyze')
            ->with($picture)
            ->willReturn(true)
        ;

        $workflow
            ->expects($this->once())
            ->method('applyAnalyzeKo')
            ->with($picture)
        ;

        $workflow
            ->expects($this->never())
            ->method('applyAnalyzeOk')
        ;

        $pictureChecker = new PictureChecker(
            $manager,
            $checker,
            $mailer,
            $workflow,
            new NullLogger(),
            '/path/to/data'
        );

        $pictureChecker->execute($picture);
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
