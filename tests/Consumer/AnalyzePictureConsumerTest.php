<?php

namespace App\Tests\Consumer;

use App\Checker\PictureChecker;
use App\Consumer\Ping;
use App\Consumer\AnalyzePictureConsumer;
use App\Entity\Picture;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class AnalyzePictureConsumerTest extends TestCase
{
    public function testPing()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $checker = $this->createMock(PictureChecker::class);
        $checker
            ->expects($this->never())
            ->method('execute')
        ;

        $consumer = new AnalyzePictureConsumer($serializer, $checker, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $picture = new Picture();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('body', Picture::class, Format::JSON, ['groups' => [Group::EVENT_ANALYZE_PICTURE]])
            ->willReturn($picture)
        ;

        $checker = $this->createMock(PictureChecker::class);
        $checker
            ->expects($this->once())
            ->method('execute')
            ->with($picture)
        ;

        $consumer = new AnalyzePictureConsumer($serializer, $checker, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
