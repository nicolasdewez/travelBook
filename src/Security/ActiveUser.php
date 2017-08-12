<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Workflow\RegistrationWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ActiveUser
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var RegistrationWorkflow */
    private $workflow;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface $manager
     * @param RegistrationWorkflow   $workflow
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $manager, RegistrationWorkflow $workflow, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->workflow = $workflow;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] User: %s', Log::SUBJECT_ACTIVE, $user->getUsername()));

        $user->setEnabled(true);

        $this->workflow->applyActive($user);

        $this->manager->flush();
    }
}
