<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Producer\MailEnableAccountProducer;
use App\Security\RefreshToken;
use App\Security\EnableAccount;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EnableAccountTest extends TestCase
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

        $producer = $this->createMock(MailEnableAccountProducer::class);
        $producer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
        ;

        $enableAccount = new EnableAccount($manager, $producer, new NullLogger());
        $enableAccount->execute($user);

        $this->assertTrue($user->isEnabled());
    }
}
