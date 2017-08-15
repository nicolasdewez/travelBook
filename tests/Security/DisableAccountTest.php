<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Producer\MailDisableAccountProducer;
use App\Security\RefreshToken;
use App\Security\DisableAccount;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DisableAccountTest extends TestCase
{
    public function testExecute()
    {
        $user = new User();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $producer = $this->createMock(MailDisableAccountProducer::class);
        $producer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
        ;

        $disableAccount = new DisableAccount($manager, $producer, new NullLogger());
        $disableAccount->execute($user);

        $this->assertFalse($user->isEnabled());
    }
}
