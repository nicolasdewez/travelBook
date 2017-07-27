<?php

namespace App\Tests\Consumer;

use App\Consumer\Ping;
use App\Consumer\RegistrationConsumer;
use App\Entity\User;
use App\Security\UserRegistration;
use App\Serializer\Formats;
use App\Serializer\Groups;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationConsumerTest extends TestCase
{
    public function testPing()
    {
        $consumer = new RegistrationConsumer(
            $this->createMock(SerializerInterface::class),
            $this->createMock(UserRegistration::class),
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
            ->with('body', User::class, Formats::JSON, ['groups' => [Groups::EVENT_REGISTRATION]])
            ->willReturn($user)
        ;

        $userRegistration = $this->createMock(UserRegistration::class);
        $userRegistration
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;
        $consumer = new RegistrationConsumer(
            $serializer,
            $userRegistration,
            new NullLogger()
        );

        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
