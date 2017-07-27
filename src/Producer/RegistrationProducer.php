<?php

namespace App\Producer;

use App\Entity\User;
use App\Logger\Log;
use App\Serializer\Formats;
use App\Serializer\Groups;

class RegistrationProducer extends AbstractProducer
{
    /**
     * @param User $user
     */
    public function execute(User $user)
    {
        $this->logger->info(sprintf('[%s] Publish message', Log::SUBJECT_REGISTRATION));

        $content = $this->serializer->serialize($user, Formats::JSON, ['groups' => [Groups::EVENT_REGISTRATION]]);

        $this->producer->publish($content);

        $this->logMessage($content, Log::SUBJECT_REGISTRATION);
    }
}
