<?php

namespace App\Mailer;

use App\Entity\User;
use App\Logger\Log;

class UpdateAccountMailer extends AbstractMailer
{
    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $message = $this->buildMessage(
            $user->getEmail(),
            Mail::SUBJECT_UPDATE_ACCOUNT,
            $user->getLocale(),
            $this->twig->render(
                'mailing/update-account.html.twig',
                [
                    'user' => $user,
                ]
            )
        );

        $this->mailer->send($message);

        $this->logger->info(sprintf('[%s] Mail sent', Log::SUBJECT_UPDATE_ACCOUNT));
    }
}
