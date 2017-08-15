<?php

namespace App\Tests\Consumer;

use App\Consumer\MailEnableAccountConsumer;
use App\Consumer\Ping;
use App\Entity\User;
use App\Mailer\EnableAccountMailer;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailEnableAccountConsumerTest extends TestCase
{
    public function testPing()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $mailer = $this->createMock(EnableAccountMailer::class);
        $mailer
            ->expects($this->never())
            ->method('execute')
        ;

        $consumer = new MailEnableAccountConsumer($serializer, $mailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $user = new User();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('body', User::class, Format::JSON, ['groups' => [Group::EVENT_ENABLE_ACCOUNT]])
            ->willReturn($user)
        ;

        $enableAccountMailer = $this->createMock(EnableAccountMailer::class);
        $enableAccountMailer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;

        $consumer = new MailEnableAccountConsumer($serializer, $enableAccountMailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
