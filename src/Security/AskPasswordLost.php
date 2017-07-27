<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Producer\PasswordLostProducer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AskPasswordLost
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var PasswordLostProducer */
    private $producer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface $manager
     * @param PasswordLostProducer   $producer
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $manager, PasswordLostProducer $producer, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        /** @var User $userInDatabase */
        $userInDatabase = $this->manager->getRepository(User::class)->findOneBy([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ]);

        if (null === $userInDatabase) {
            $this->logger->info(sprintf(
                '[%s] No users found for username %s and email %s',
                Log::SUBJECT_PASSWORD_LOST,
                $user->getUsername(),
                $user->getEmail()
            ));

            return;
        }

        $this->logger->info(sprintf(
            '[%s] Ask for %s and %s',
            Log::SUBJECT_REGISTRATION,
            $user->getUsername(),
            $user->getEmail()
        ));

        $userInDatabase->setEnabled(false);

        $this->manager->flush();

        $this->producer->execute($userInDatabase);
    }
}
