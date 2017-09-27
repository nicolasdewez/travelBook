<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Producer\MailUpdateAccountProducer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdateAccount
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var RefreshToken */
    private $refreshToken;

    /** @var MailUpdateAccountProducer */
    private $producer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param RefreshToken                 $refreshToken
     * @param MailUpdateAccountProducer    $producer
     * @param LoggerInterface              $logger
     */
    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        RefreshToken $refreshToken,
        MailUpdateAccountProducer $producer,
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
        $this->logger->info(sprintf('[%s] User: %s', Log::SUBJECT_UPDATE_ACCOUNT, $user->getUsername()));

        if (null !== $user->getNewPassword()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getNewPassword()));
        }

        $this->manager->flush();

        $this->refreshToken->execute($user);

        if (!$user->isEmailNotification()) {
            return;
        }

        $this->producer->execute($user);
    }
}
