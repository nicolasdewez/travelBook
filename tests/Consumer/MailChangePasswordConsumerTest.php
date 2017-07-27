<?php

namespace App\Tests\Consumer;

use App\Consumer\MailChangePasswordConsumer;
use App\Consumer\Ping;
use App\Entity\User;
use App\Mailer\ChangePasswordMailer;
use App\Serializer\Formats;
use App\Serializer\Groups;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailChangePasswordConsumerTest extends TestCase
{
    public function testPing()
    {
        $consumer = new MailChangePasswordConsumer(
            $this->createMock(SerializerInterface::class),
            $this->createMock(ChangePasswordMailer::class),
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
            ->with('body', User::class, Formats::JSON, ['groups' => [Groups::EVENT_CHANGE_PASSWORD]])
            ->willReturn($user)
        ;

        $updateAccountMailer = $this->createMock(ChangePasswordMailer::class);
        $updateAccountMailer
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;
        $consumer = new MailChangePasswordConsumer(
            $serializer,
            $updateAccountMailer,
            new NullLogger()
        );

        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
