<?php

namespace App\Tests\Producer;

use App\Entity\User;
use App\Producer\MailChangePasswordProducer;
use App\Serializer\Format;
use App\Serializer\Group;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;

class MailChangePasswordProducerTest extends TestCase
{
    public function testExecute()
    {
        $user = new User();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($user, Format::JSON, ['groups' => [Group::EVENT_CHANGE_PASSWORD]])
            ->willReturn('content')
        ;

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->with('content')
            ->willReturn(true)
        ;

        $registrationProducer = new MailChangePasswordProducer(
            $producer,
            $serializer,
            new NullLogger()
        );

        $registrationProducer->execute($user);
    }
}
