<?php

namespace App\Producer;

use App\Entity\InvalidationPicture;
use App\Logger\Log;
use App\Serializer\Format;
use App\Serializer\Group;

class MailInvalidPictureProducer extends AbstractProducer
{
    /**
     * @param InvalidationPicture $invalidationPicture
     */
    public function execute(InvalidationPicture $invalidationPicture)
    {
        $this->logger->info(sprintf('[%s] Publish message', Log::SUBJECT_PICTURE_INVALIDATION));

        $content = $this->serializer->serialize($invalidationPicture, Format::JSON, ['groups' => [Group::EVENT_INVALID_PICTURE]]);

        $this->producer->publish($content);

        $this->logMessage($content, Log::SUBJECT_PICTURE_INVALIDATION);
    }
}
