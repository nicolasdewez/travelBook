<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Producer\MailChangePasswordProducer;
use App\Security\RefreshToken;
use App\Security\ValidFirstConnection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ValidFirstConnectionTest extends TestCase
{
    public function testExecute()
    {
        $user = (new User())->setNewPassword('password');

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

        $refreshToken = $this->createMock(RefreshToken::class);
        $refreshToken
            ->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
        ;

        $producer = $this->createMock(MailChangePasswordProducer::class);
        $producer
            ->expects($this->once())
            ->method('execute')
            ->with($user)
        ;

        $validFirstConnection = new ValidFirstConnection(
            $manager,
            $encoder,
            $refreshToken,
            $producer,
            new NullLogger()
        );

        $validFirstConnection->execute($user);

        $this->assertSame('encoded', $user->getPassword());
        $this->assertFalse($user->isFirstConnection());
    }

    public function testExecuteWithoutMail()
    {
        $user = (new User())
            ->setNewPassword('password')
            ->setEmailNotification(false)
        ;

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

        $refreshToken = $this->createMock(RefreshToken::class);
        $refreshToken
            ->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
        ;

        $producer = $this->createMock(MailChangePasswordProducer::class);
        $producer
            ->expects($this->never())
            ->method('execute')
        ;

        $validFirstConnection = new ValidFirstConnection(
            $manager,
            $encoder,
            $refreshToken,
            $producer,
            new NullLogger()
        );

        $validFirstConnection->execute($user);

        $this->assertSame('encoded', $user->getPassword());
        $this->assertFalse($user->isFirstConnection());
    }
}
