<?php

namespace App\Tests\Consumer;

use App\Consumer\MailDisableAccountConsumer;
use App\Consumer\Ping;
use App\Entity\User;
use App\Mailer\DisableAccountMailer;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailDisableAccountConsumerTest extends TestCase
{
    public function testPing()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $mailer = $this->createMock(DisableAccountMailer::class);
        $mailer
            ->expects($this->never())
            ->method('execute')
        ;

        $consumer = new MailDisableAccountConsumer($serializer, $mailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $user = new User();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('body', User::class, Format::JSON, ['groups' => [Group::EVENT_DISABLE_ACCOUNT]])
            ->willReturn($user)
        ;

        $disableAccountMailer = $this->createMock(DisableAccountMailer::class);
        $disableAccountMailer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;

        $consumer = new MailDisableAccountConsumer($serializer, $disableAccountMailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
