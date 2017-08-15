<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Producer\RegistrationProducer;
use App\Security\AskRegistration;
use App\Security\AskResendRegistration;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class AskResendRegistrationTest extends TestCase
{
    public function testExecute()
    {
        $user = new User();

        $producer = $this->createMock(RegistrationProducer::class);
        $producer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
        ;

        $askRegistration = new AskResendRegistration($producer, new NullLogger());
        $askRegistration->execute($user);
    }
}
