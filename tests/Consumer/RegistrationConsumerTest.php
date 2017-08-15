<?php

namespace App\Tests\Consumer;

use App\Consumer\Ping;
use App\Consumer\RegistrationConsumer;
use App\Entity\User;
use App\Security\UserRegistration;
use App\Serializer\Format;
use App\Serializer\Group;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationConsumerTest extends TestCase
{
    public function testPing()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $mailer = $this->createMock(UserRegistration::class);
        $mailer
            ->expects($this->never())
            ->method('execute')
        ;

        $consumer = new RegistrationConsumer($serializer, $mailer, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage(Ping::BODY)));
    }

    public function testExecute()
    {
        $user = new User();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('body', User::class, Format::JSON, ['groups' => [Group::EVENT_REGISTRATION]])
            ->willReturn($user)
        ;

        $userRegistration = $this->createMock(UserRegistration::class);
        $userRegistration
            ->expects($this->once())
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;

        $consumer = new RegistrationConsumer($serializer, $userRegistration, new NullLogger());
        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
