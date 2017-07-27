<?php

namespace App\Consumer;

use App\Entity\User;
use App\Logger\Log;
use App\Security\UserRegistration;
use App\Serializer\Formats;
use App\Serializer\Groups;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationConsumer extends AbstractConsumer
{
    /** @var UserRegistration */
    private $userRegistration;

    /**
     * @param SerializerInterface $serializer
     * @param UserRegistration    $userRegistration
     * @param LoggerInterface     $logger
     */
    public function __construct(SerializerInterface $serializer, UserRegistration $userRegistration, LoggerInterface $logger)
    {
        parent::__construct($serializer, $logger);
        $this->userRegistration = $userRegistration;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $message): bool
    {
        if ($this->isPing($message)) {
            return true;
        }

        $this->logMessage($message, Log::SUBJECT_REGISTRATION);

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $message->getBody(),
            User::class,
            Formats::JSON,
            ['groups' => [Groups::EVENT_REGISTRATION]]
        );

        $this->userRegistration->execute($user);

        return true;
    }
}
