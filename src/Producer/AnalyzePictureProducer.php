<?php

namespace App\Producer;

use App\Entity\Picture;
use App\Logger\Log;
use App\Serializer\Format;
use App\Serializer\Group;

class AnalyzePictureProducer extends AbstractProducer
{
    /**
     * @param Picture $picture
     */
    public function execute(Picture $picture)
    {
        $this->logger->info(sprintf('[%s] Publish message', Log::SUBJECT_ANALYZE_PICTURE));

        $content = $this->serializer->serialize($picture, Format::JSON, ['groups' => [Group::EVENT_ANALYZE_PICTURE]]);

        $this->producer->publish($content);

        $this->logMessage($content, Log::SUBJECT_ANALYZE_PICTURE);
    }
}
