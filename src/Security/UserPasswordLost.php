<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Mailer\PasswordLostMailer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordLost
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var GeneratePassword */
    private $generatePassword;

    /** @var PasswordLostMailer */
    private $mailer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param GeneratePassword             $generatePassword
     * @param PasswordLostMailer           $mailer
     * @param LoggerInterface              $logger
     */
    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        GeneratePassword $generatePassword,
        PasswordLostMailer $mailer,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->generatePassword = $generatePassword;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $plainPassword = $this->generatePassword->execute();

        $user
            ->setPassword($this->encoder->encodePassword($user, $plainPassword))
            ->setEnabled(true)
            ->setFirstConnection(true)
        ;

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] Password generated', Log::SUBJECT_PASSWORD_LOST));

        $this->mailer->execute($user, $plainPassword);
    }
}
