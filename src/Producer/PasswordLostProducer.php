<?php

namespace App\Producer;

use App\Entity\User;
use App\Logger\Log;
use App\Serializer\Format;
use App\Serializer\Group;

class PasswordLostProducer extends AbstractProducer
{
    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] Publish message', Log::SUBJECT_PASSWORD_LOST));

        $content = $this->serializer->serialize($user, Format::JSON, ['groups' => [Group::EVENT_PASSWORD_LOST]]);

        $this->producer->publish($content);

        $this->logMessage($content, Log::SUBJECT_PASSWORD_LOST);
    }
}
