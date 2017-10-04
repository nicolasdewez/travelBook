<?php

namespace App\Tests\Manager;

use App\Entity\Feedback;
use App\Entity\User;
use App\Manager\FeedbackManager;
use App\Model\FilterFeedback;
use App\Pagination\InformationPagination;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FeedbackManagerTest extends TestCase
{
    public function testSave()
    {
        $feedback = (new Feedback())->setUser(new User());

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('persist')
            ->with($feedback)
        ;

        $manager
            ->expects($this->exactly(2))
            ->method('flush')
            ->withAnyParameters()
        ;

        $feedbackManager = new FeedbackManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $feedbackManager->save($feedback);

        $class = new \ReflectionClass($feedback);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($feedback, 1);

        $feedbackManager->save($feedback);
    }

    public function testCountElements()
    {
        $filterFeedback = new FilterFeedback();

        $repository = $this->createMock(FeedbackRepository::class);
        $repository
            ->expects($this->once())
            ->method('countByCriteria')
            ->with($filterFeedback)
            ->willReturn(12)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Feedback::class)
            ->willReturn($repository)
        ;

        $feedbackManager = new FeedbackManager(
            $manager,
            $this->createMock(InformationPagination::class),
            new NullLogger()
        );

        $this->assertSame(12, $feedbackManager->countElements($filterFeedback));
    }

    public function testListElements()
    {
        $filterFeedback = new FilterFeedback();

        $repository = $this->createMock(FeedbackRepository::class);
        $repository
            ->expects($this->once())
            ->method('getByCriteria')
            ->with($filterFeedback, ['limit' => 25, 'offset' => 0])
            ->willReturn(['element1', 'element2'])
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Feedback::class)
            ->willReturn($repository)
        ;

        $pagination = $this->createMock(InformationPagination::class);
        $pagination
            ->expects($this->once())
            ->method('getLimitAndOffset')
            ->with(1)
            ->willReturn(['limit' => 25, 'offset' => 0])
        ;

        $feedbackManager = new FeedbackManager(
            $manager,
            $pagination,
            new NullLogger()
        );

        $this->assertSame(['element1', 'element2'], $feedbackManager->listElements($filterFeedback, 1));
    }
}
