<?php

namespace App\Consumer;

use App\Entity\User;
use App\Logger\Log;
use App\Mailer\ChangePasswordMailer;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MailChangePasswordConsumer extends AbstractConsumer
{
    /** @var ChangePasswordMailer */
    private $mailer;

    /**
     * @param SerializerInterface  $serializer
     * @param LoggerInterface      $logger
     * @param ChangePasswordMailer $mailer
     */
    public function __construct(SerializerInterface $serializer, ChangePasswordMailer $mailer, LoggerInterface $logger)
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

        $this->logMessage($message, Log::SUBJECT_CHANGE_PASSWORD);

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $message->getBody(),
            User::class,
            Format::JSON,
            ['groups' => [Group::EVENT_CHANGE_PASSWORD]]
        );

        $this->mailer->execute($user);

        return true;
    }
}
