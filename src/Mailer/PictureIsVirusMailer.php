<?php

namespace App\Mailer;

use App\Entity\Picture;
use App\Logger\Log;

class PictureIsVirusMailer extends AbstractMailer
{
    /**
     * @param Picture $picture
     */
    public function execute(Picture $picture)
    {
        $user = $picture->getTravel()->getUser();

        $message = $this->buildMessage(
            $user->getEmail(),
            Mail::SUBJECT_PICTURE_VIRUS,
            $user->getLocale(),
            $this->twig->render(
                'mailing/picture-virus.html.twig',
                [
                    'picture' => $picture,
                ]
            )
        );

        $this->mailer->send($message);

        $this->logger->info(sprintf('[%s] Mail sent', Log::SUBJECT_PICTURE_VIRUS));
    }
}
