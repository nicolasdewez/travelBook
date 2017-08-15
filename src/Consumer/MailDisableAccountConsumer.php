<?php

namespace App\Consumer;

use App\Entity\User;
use App\Logger\Log;
use App\Mailer\DisableAccountMailer;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MailDisableAccountConsumer extends AbstractConsumer
{
    /** @var DisableAccountMailer */
    private $mailer;

    /**
     * @param SerializerInterface  $serializer
     * @param LoggerInterface      $logger
     * @param DisableAccountMailer $mailer
     */
    public function __construct(SerializerInterface $serializer, DisableAccountMailer $mailer, LoggerInterface $logger)
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

        $this->logMessage($message, Log::SUBJECT_DISABLE_ACCOUNT);

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $message->getBody(),
            User::class,
            Format::JSON,
            ['groups' => [Group::EVENT_DISABLE_ACCOUNT]]
        );

        $this->mailer->execute($user);

        return true;
    }
}
