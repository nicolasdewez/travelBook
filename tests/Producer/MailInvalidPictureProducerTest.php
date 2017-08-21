<?php

namespace App\Tests\Producer;

use App\Entity\InvalidationPicture;
use App\Producer\MailInvalidPictureProducer;
use App\Serializer\Format;
use App\Serializer\Group;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailInvalidPictureProducerTest extends TestCase
{
    public function testExecute()
    {
        $invalidationPicture = new InvalidationPicture();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($invalidationPicture, Format::JSON, ['groups' => [Group::EVENT_INVALID_PICTURE]])
            ->willReturn('content')
        ;

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->with('content')
            ->willReturn(true)
        ;

        $mailInvalidPictureProducer = new MailInvalidPictureProducer(
            $producer,
            $serializer,
            new NullLogger()
        );

        $mailInvalidPictureProducer->execute($invalidationPicture);
    }
}
