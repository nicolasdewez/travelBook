<?php

namespace App\Tests\Feedback;

use App\Entity\Feedback;
use App\Feedback\ProcessFeedback;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ProcessFeedbackTest extends TestCase
{
    public function testExecute()
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $feedback = new Feedback();

        $processFeedback = new ProcessFeedback($manager, new NullLogger());
        $processFeedback->execute($feedback);

        $this->assertTrue($feedback->isProcessed());
    }
}
