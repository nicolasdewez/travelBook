<?php

namespace App\Tests\Producer;

use App\Entity\Picture;
use App\Producer\AnalyzePictureProducer;
use App\Serializer\Format;
use App\Serializer\Group;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class AnalyzePictureProducerTest extends TestCase
{
    public function testExecute()
    {
        $picture = new Picture();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($picture, Format::JSON, ['groups' => [Group::EVENT_ANALYZE_PICTURE]])
            ->willReturn('content')
        ;

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->with('content')
            ->willReturn(true)
        ;

        $analyzePictureProducer = new AnalyzePictureProducer(
            $producer,
            $serializer,
            new NullLogger()
        );

        $analyzePictureProducer->execute($picture);
    }
}
