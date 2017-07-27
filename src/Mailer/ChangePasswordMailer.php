<?php

namespace App\Mailer;

use App\Entity\User;
use App\Logger\Log;

class ChangePasswordMailer extends AbstractMailer
{
    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $message = $this->buildMessage(
            $user->getEmail(),
            Mail::SUBJECT_CHANGE_PASSWORD,
            $user->getLocale(),
            $this->twig->render(
                'mailing/change-password.html.twig',
                [
                    'user' => $user,
                ]
            )
        );

        $this->mailer->send($message);

        $this->logger->info(sprintf('[%s] Mail sent', Log::SUBJECT_CHANGE_PASSWORD));
    }
}
