<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractConsumer implements ConsumerInterface
{
    /** @var SerializerInterface */
    protected $serializer;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param SerializerInterface $serializer
     * @param LoggerInterface     $logger
     */
    public function __construct(SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return bool
     */
    protected function isPing(AMQPMessage $message): bool
    {
        return Ping::BODY === $message->getBody();
    }

    /**
     * @param AMQPMessage $message
     * @param string      $subject
     */
    protected function logMessage(AMQPMessage $message, string $subject)
    {
        $this->logger->info(sprintf('[%s] Message received: %s', $subject, $message->getBody()));
    }
}
