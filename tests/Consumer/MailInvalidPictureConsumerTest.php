<?php

namespace App\Tests\Consumer;

use App\Consumer\MailChangePasswordConsumer;
use App\Consumer\MailInvalidPictureConsumer;
use App\Consumer\Ping;
use App\Entity\InvalidationPicture;
use App\Entity\User;
use App\Mailer\ChangePasswordMailer;
use App\Mailer\InvalidPictureMailer;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailInvalidPictureConsumerTest extends TestCase
{
    public function testPing()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $mailer = $this->createMock(InvalidPictureMailer::class);
        $mailer
            ->expects($this->never())
            ->method('execute')
        ;

        $consumer = new MailInvalidPictureConsumer($serializer, $mailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $invalidationPicture = new InvalidationPicture();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('body', InvalidationPicture::class, Format::JSON, ['groups' => [Group::EVENT_INVALID_PICTURE]])
            ->willReturn($invalidationPicture)
        ;

        $mailer = $this->createMock(InvalidPictureMailer::class);
        $mailer
            ->expects($this->once())
            ->method('execute')
            ->with($invalidationPicture)
        ;

        $consumer = new MailInvalidPictureConsumer($serializer, $mailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
