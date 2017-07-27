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
        $consumer = new PasswordLostConsumer(
            $this->createMock(SerializerInterface::class),
            $this->createMock(UserPasswordLost::class),
            new NullLogger()
        );

        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $user = new User();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->method('deserialize')
            ->with('body', User::class, Formats::JSON, ['groups' => [Groups::EVENT_PASSWORD_LOST]])
            ->willReturn($user)
        ;

        $userPasswordLost = $this->createMock(UserPasswordLost::class);
        $userPasswordLost
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;
        $consumer = new PasswordLostConsumer(
            $serializer,
            $userPasswordLost,
            new NullLogger()
        );

        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
