<?php

namespace App\Consumer;

use App\Entity\User;
use App\Logger\Log;
use App\Mailer\UpdateAccountMailer;
use App\Serializer\Formats;
use App\Serializer\Groups;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MailUpdateAccountConsumer extends AbstractConsumer
{
    /** @var UpdateAccountMailer */
    private $mailer;

    /**
     * @param SerializerInterface $serializer
     * @param LoggerInterface     $logger
     * @param UpdateAccountMailer $mailer
     */
    public function __construct(SerializerInterface $serializer, UpdateAccountMailer $mailer, LoggerInterface $logger)
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

        $this->logMessage($message, Log::SUBJECT_UPDATE_ACCOUNT);

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $message->getBody(),
            User::class,
            Formats::JSON,
            ['groups' => [Groups::EVENT_UPDATE_ACCOUNT]]
        );

        $this->mailer->execute($user);

        return true;
    }
}
