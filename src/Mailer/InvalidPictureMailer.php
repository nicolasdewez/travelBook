<?php

namespace App\Mailer;

use App\Entity\InvalidationPicture;
use App\Logger\Log;

class InvalidPictureMailer extends AbstractMailer
{
    /**
     * @param InvalidationPicture $invalidationPicture
     */
    public function execute(InvalidationPicture $invalidationPicture)
    {
        $user = $invalidationPicture->getPicture()->getTravel()->getUser();

        $message = $this->buildMessage(
            $user->getEmail(),
            Mail::SUBJECT_PICTURE_INVALID,
            $user->getLocale(),
            $this->twig->render(
                'mailing/invalid-picture.html.twig',
                [
                    'invalidationPicture' => $invalidationPicture,
                ]
            )
        );

        $this->mailer->send($message);

        $this->logger->info(sprintf('[%s] Mail sent', Log::SUBJECT_PICTURE_INVALIDATION));
    }
}
