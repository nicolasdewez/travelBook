<?php

namespace App\Tests\Consumer;

use App\Consumer\MailUpdateAccountConsumer;
use App\Consumer\Ping;
use App\Entity\User;
use App\Mailer\UpdateAccountMailer;
use App\Serializer\Formats;
use App\Serializer\Groups;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailUpdateAccountConsumerTest extends TestCase
{
    public function testPing()
    {
        $consumer = new MailUpdateAccountConsumer(
            $this->createMock(SerializerInterface::class),
            $this->createMock(UpdateAccountMailer::class),
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
            ->with('body', User::class, Formats::JSON, ['groups' => [Groups::EVENT_UPDATE_ACCOUNT]])
            ->willReturn($user)
        ;

        $updateAccountMailer = $this->createMock(UpdateAccountMailer::class);
        $updateAccountMailer
            ->method('execute')
            ->with($user)
            ->willReturn(true)
        ;
        $consumer = new MailUpdateAccountConsumer(
            $serializer,
            $updateAccountMailer,
            new NullLogger()
        );

        $this->assertTrue($consumer->execute(new AMQPMessage('body')));
    }
}
