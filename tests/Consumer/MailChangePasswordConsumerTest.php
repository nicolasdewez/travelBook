<?php

namespace App\Tests\Consumer;

use App\Consumer\MailChangePasswordConsumer;
use App\Consumer\Ping;
use App\Entity\User;
use App\Mailer\ChangePasswordMailer;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailChangePasswordConsumerTest extends TestCase
{
    public function testPing()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $mailer = $this->createMock(ChangePasswordMailer::class);
        $mailer
            ->expects($this->never())
            ->method('execute')
        ;

        $consumer = new MailChangePasswordConsumer($serializer, $mailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $user = new User();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('body', User::class, Format::JSON, ['groups' => [Group::EVENT_CHANGE_PASSWORD]])
            ->willReturn($user)
        ;

        $updateAccountMailer = $this->createMock(ChangePasswordMailer::class);
        $updateAccountMailer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;

        $consumer = new MailChangePasswordConsumer($serializer, $updateAccountMailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
