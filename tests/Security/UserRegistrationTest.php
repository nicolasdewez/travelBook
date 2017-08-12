<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Mailer\RegistrationMailer;
use App\Security\GeneratePassword;
use App\Security\GenerateRegistrationCode;
use App\Security\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationTest extends TestCase
{
    public function testExecute()
    {
        $user = (new User())->setUsername('ndewez');

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

        $generateRegistrationCode = $this->createMock(GenerateRegistrationCode::class);
        $generateRegistrationCode
            ->expects($this->once())
            ->method('execute')
            ->with('ndewez')
            ->willReturn('code')
        ;

        $mailer = $this->createMock(RegistrationMailer::class);
        $mailer
            ->expects($this->once())
            ->method('execute')
            ->with($user, 'password')
        ;

        $userRegistration = new UserRegistration(
            $manager,
            $encoder,
            $generatePassword,
            $generateRegistrationCode,
            $mailer,
            new NullLogger()
        );

        $userRegistration->execute($user);

        $this->assertSame('encoded', $user->getPassword());
        $this->assertSame('code', $user->getRegistrationCode());
    }
}
