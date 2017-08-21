<?php

namespace App\Consumer;

use App\Checker\PictureChecker;
use App\Entity\Picture;
use App\Logger\Log;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AnalyzePictureConsumer extends AbstractConsumer
{
    /** @var PictureChecker */
    private $checker;

    /**
     * @param SerializerInterface $serializer
     * @param PictureChecker      $checker
     * @param LoggerInterface     $logger
     */
    public function __construct(SerializerInterface $serializer, PictureChecker $checker, LoggerInterface $logger)
    {
        parent::__construct($serializer, $logger);
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $message): bool
    {
        if ($this->isPing($message)) {
            return true;
        }

        $this->logMessage($message, Log::SUBJECT_ANALYZE_PICTURE);

        /** @var Picture $picture */
        $picture = $this->serializer->deserialize(
            $message->getBody(),
            Picture::class,
            Format::JSON,
            ['groups' => [Group::EVENT_ANALYZE_PICTURE]]
        );

        $this->checker->execute($picture);

        return true;
    }
}
