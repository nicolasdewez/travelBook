<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Mailer\PasswordLostMailer;
use App\Security\GeneratePassword;
use App\Security\UserPasswordLost;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordLostTest extends TestCase
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

        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with($user, 'password')
            ->willReturn('encoded')
        ;

        $generatePassword = $this->createMock(GeneratePassword::class);
        $generatePassword
            ->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn('password')
        ;

        $mailer = $this->createMock(PasswordLostMailer::class);
        $mailer
            ->expects($this->once())
            ->method('execute')
            ->with($user, 'password')
        ;

        $userPasswordLost = new UserPasswordLost(
            $manager,
            $encoder,
            $generatePassword,
            $mailer,
            new NullLogger()
        );

        $userPasswordLost->execute($user);

        $this->assertSame('encoded', $user->getPassword());
        $this->assertTrue($user->isEnabled());
        $this->assertTrue($user->isFirstConnection());
    }
}
