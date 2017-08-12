<?php

namespace App\Tests\Consumer;

use App\Consumer\PasswordLostConsumer;
use App\Consumer\Ping;
use App\Entity\User;
use App\Security\UserPasswordLost;
use App\Serializer\Formats;
use App\Serializer\Groups;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class PasswordLostConsumerTest extends TestCase
{
    public function testPing()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $mailer = $this->createMock(UserPasswordLost::class);
        $mailer
            ->expects($this->never())
            ->method('execute')
        ;

        $consumer = new PasswordLostConsumer($serializer, $mailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $user = new User();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('body', User::class, Formats::JSON, ['groups' => [Groups::EVENT_PASSWORD_LOST]])
            ->willReturn($user)
        ;

        $userPasswordLost = $this->createMock(UserPasswordLost::class);
        $userPasswordLost
            ->expects($this->once())
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;

        $consumer = new PasswordLostConsumer($serializer, $userPasswordLost, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
