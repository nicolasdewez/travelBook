<?php

namespace App\Producer;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractProducer
{
    /** @var ProducerInterface */
    protected $producer;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param ProducerInterface   $producer
     * @param SerializerInterface $serializer
     * @param LoggerInterface     $logger
     */
    public function __construct(ProducerInterface $producer, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->producer = $producer;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @param string $content
     * @param string $subject
     */
    protected function logMessage(string $content, string $subject)
    {
        $this->logger->info(sprintf('[%s] Message sent: %s', $subject, $content));
    }
}
