<?php

namespace App\Producer;

use App\Entity\User;
use App\Logger\Log;
use App\Serializer\Formats;
use App\Serializer\Groups;

class MailUpdateAccountProducer extends AbstractProducer
{
    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] Publish message', Log::SUBJECT_UPDATE_ACCOUNT));

        $content = $this->serializer->serialize($user, Formats::JSON, ['groups' => [Groups::EVENT_UPDATE_ACCOUNT]]);

        $this->producer->publish($content);

        $this->logMessage($content, Log::SUBJECT_UPDATE_ACCOUNT);
    }
}
