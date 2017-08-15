<?php

namespace App\Producer;

use App\Entity\User;
use App\Logger\Log;
use App\Serializer\Format;
use App\Serializer\Group;

class MailUpdateAccountProducer extends AbstractProducer
{
    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] Publish message', Log::SUBJECT_UPDATE_ACCOUNT));

        $content = $this->serializer->serialize($user, Format::JSON, ['groups' => [Group::EVENT_UPDATE_ACCOUNT]]);

        $this->producer->publish($content);

        $this->logMessage($content, Log::SUBJECT_UPDATE_ACCOUNT);
    }
}
