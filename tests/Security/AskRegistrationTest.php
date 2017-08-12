<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Producer\RegistrationProducer;
use App\Security\AskRegistration;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class AskRegistrationTest extends TestCase
{
    public function testExecute()
    {
        $user = new User();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('persist')
            ->with($user)
        ;
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $producer = $this->createMock(RegistrationProducer::class);
        $producer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
        ;

        $askRegistration = new AskRegistration($manager, $producer, new NullLogger());
        $askRegistration->execute($user);

        $this->assertSame('', $user->getPassword());
        $this->assertSame('', $user->getRegistrationCode());
    }
}
