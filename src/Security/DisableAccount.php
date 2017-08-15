<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Producer\MailDisableAccountProducer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DisableAccount
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var MailDisableAccountProducer */
    private $producer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface     $manager
     * @param MailDisableAccountProducer $producer
     * @param LoggerInterface            $logger
     */
    public function __construct(
        EntityManagerInterface $manager,
        MailDisableAccountProducer $producer,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] User: %s', Log::SUBJECT_DISABLE_ACCOUNT, $user->getUsername()));

        $user->setEnabled(false);

        $this->manager->flush();

        $this->producer->execute($user);
    }
}
