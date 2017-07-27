<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Mailer\RegistrationMailer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistration
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var GeneratePassword */
    private $generatePassword;

    /** @var GenerateRegistrationCode */
    private $generateRegistrationCode;

    /** @var RegistrationMailer */
    private $mailer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param GeneratePassword             $generatePassword
     * @param GenerateRegistrationCode     $generateRegistrationCode
     * @param RegistrationMailer           $mailer
     * @param LoggerInterface              $logger
     */
    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        GeneratePassword $generatePassword,
        GenerateRegistrationCode $generateRegistrationCode,
        RegistrationMailer $mailer,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->generatePassword = $generatePassword;
        $this->generateRegistrationCode = $generateRegistrationCode;
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
            ->setRegistrationCode($this->generateRegistrationCode->execute($user->getUsername()))
        ;

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] Password and registration code generated', Log::SUBJECT_REGISTRATION));

        $this->mailer->execute($user, $plainPassword);
    }
}
