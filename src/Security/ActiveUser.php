<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ActiveUser
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
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] User: %s', Log::SUBJECT_ACTIVE, $user->getUsername()));

        $user
            ->setRegistrationInProgress(false)
            ->setEnabled(true)
        ;

        $this->manager->flush();
    }
}
