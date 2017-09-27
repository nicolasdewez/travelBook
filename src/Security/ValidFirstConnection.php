<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Producer\MailChangePasswordProducer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ValidFirstConnection
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var RefreshToken */
    private $refreshToken;

    /** @var MailChangePasswordProducer */
    private $producer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param RefreshToken                 $refreshToken
     * @param MailChangePasswordProducer   $producer
     * @param LoggerInterface              $logger
     */
    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        RefreshToken $refreshToken,
        MailChangePasswordProducer $producer,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->refreshToken = $refreshToken;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] Valid first connection for user: %s', Log::SUBJECT_CHANGE_PASSWORD, $user->getUsername()));

        $user
            ->setPassword($this->encoder->encodePassword($user, $user->getNewPassword()))
            ->setFirstConnection(false)
        ;

        $this->manager->flush();

        $this->refreshToken->execute($user);

        if (!$user->isEmailNotification()) {
            return;
        }

        $this->producer->execute($user);
    }
}
