<?php

namespace App\Consumer;

use App\Entity\InvalidationPicture;
use App\Logger\Log;
use App\Mailer\InvalidPictureMailer;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MailInvalidPictureConsumer extends AbstractConsumer
{
    /** @var InvalidPictureMailer */
    private $mailer;

    /**
     * @param SerializerInterface  $serializer
     * @param LoggerInterface      $logger
     * @param InvalidPictureMailer $mailer
     */
    public function __construct(SerializerInterface $serializer, InvalidPictureMailer $mailer, LoggerInterface $logger)
    {
        parent::__construct($serializer, $logger);
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $message): bool
    {
        if ($this->isPing($message)) {
            return true;
        }

        $this->logMessage($message, Log::SUBJECT_PICTURE_INVALIDATION);

        /** @var InvalidationPicture $invalidationPicture */
        $invalidationPicture = $this->serializer->deserialize(
            $message->getBody(),
            InvalidationPicture::class,
            Format::JSON,
            ['groups' => [Group::EVENT_INVALID_PICTURE]]
        );

        $this->mailer->execute($invalidationPicture);

        return true;
    }
}
