<?php

namespace App\Mailer;

use App\Entity\User;
use App\Logger\Log;

class RegistrationMailer extends AbstractMailer
{
    /**
     * @param User   $user
     * @param string $password
     */
    public function execute(User $user, string $password)
    {
        $message = $this->buildMessage(
            $user->getEmail(),
            Mail::SUBJECT_REGISTRATION,
            $user->getLocale(),
            $this->twig->render(
                'mailing/registration.html.twig',
                [
                    'user' => $user,
                    'password' => $password,
                ]
            )
        );

        $this->mailer->send($message);

        $this->logger->info(sprintf('[%s] Mail sent', Log::SUBJECT_REGISTRATION));
    }
}
