<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Mailer\RegistrationMailer;
use App\Workflow\RegistrationWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistration
{
    /** @var RegistrationWorkflow */
    private $workflow;

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
     * @param RegistrationWorkflow         $workflow
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param GeneratePassword             $generatePassword
     * @param GenerateRegistrationCode     $generateRegistrationCode
     * @param RegistrationMailer           $mailer
     * @param LoggerInterface              $logger
     */
    public function __construct(
        RegistrationWorkflow $workflow,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder,
        GeneratePassword $generatePassword,
        GenerateRegistrationCode $generateRegistrationCode,
        RegistrationMailer $mailer,
        LoggerInterface $logger
    ) {
        $this->workflow = $workflow;
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
        if (!$this->workflow->canApplyRegistration($user)) {
            $this->logger->error(sprintf(
                'User %s can not be registered because workflow not support this.',
                $user->getUsername()
            ));

            return;
        }

        $plainPassword = $this->generatePassword->execute();

        $user
            ->setPassword($this->encoder->encodePassword($user, $plainPassword))
            ->setRegistrationCode($this->generateRegistrationCode->execute($user->getUsername()))
        ;

        $this->workflow->applyRegistration($user);

        $this->manager->flush();

        $this->logger->info(sprintf('[%s] Password and registration code generated', Log::SUBJECT_REGISTRATION));

        $this->mailer->execute($user, $plainPassword);
    }
}
