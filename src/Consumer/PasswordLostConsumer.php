<?php

namespace App\Consumer;

use App\Entity\User;
use App\Logger\Log;
use App\Security\UserPasswordLost;
use App\Serializer\Formats;
use App\Serializer\Groups;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PasswordLostConsumer extends AbstractConsumer
{
    /** @var UserPasswordLost */
    private $userPasswordLost;

    /**
     * @param SerializerInterface $serializer
     * @param UserPasswordLost    $userPasswordLost
     * @param LoggerInterface     $logger
     */
    public function __construct(SerializerInterface $serializer, UserPasswordLost $userPasswordLost, LoggerInterface $logger)
    {
        parent::__construct($serializer, $logger);
        $this->userPasswordLost = $userPasswordLost;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $message): bool
    {
        if ($this->isPing($message)) {
            return true;
        }

        $this->logMessage($message, Log::SUBJECT_PASSWORD_LOST);

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $message->getBody(),
            User::class,
            Formats::JSON,
            ['groups' => [Groups::EVENT_PASSWORD_LOST]]
        );

        $this->userPasswordLost->execute($user);

        return true;
    }
}
