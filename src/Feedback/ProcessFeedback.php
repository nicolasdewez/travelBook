<?php

namespace App\Feedback;

use App\Entity\Feedback;
use App\Logger\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ProcessFeedback
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface $manager
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    /**
     * @param Feedback $feedback
     */
    public function execute(Feedback $feedback)
    {
        $this->logger->info(sprintf('[%s] User: %s', Log::SUBJECT_PROCESS_FEEDBACK, $feedback->getId()));

        $feedback->setProcessed(true);

        $this->manager->flush();
    }
}
