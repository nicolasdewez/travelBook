<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Producer\PasswordLostProducer;
use App\Security\AskPasswordLost;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class AskPasswordLostTest extends TestCase
{
    public function testExecuteWithUserUnknown()
    {
        $user = (new User())
            ->setUsername('username')
            ->setEmail('email')
        ;

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['username' => $user->getUsername(), 'email' => $user->getEmail()])
            ->willReturn(null)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $manager
            ->expects($this->never())
            ->method('flush')
        ;

        $producer = $this->createMock(PasswordLostProducer::class);
        $producer
            ->expects($this->never())
            ->method('execute')
        ;

        $askPasswordLost = new AskPasswordLost(
            $manager,
            $this->createMock(PasswordLostProducer::class),
            new NullLogger()
        );

        $askPasswordLost->execute($user);
    }

    public function testExecute()
    {
        $user = (new User())
            ->setUsername('username')
            ->setEmail('email')
        ;

        $userInDatabase = (new User())->setEnabled(true);

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['username' => $user->getUsername(), 'email' => $user->getEmail()])
            ->willReturn($userInDatabase)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $manager
            ->expects($this->once())
            ->method('flush')
        ;

        $producer = $this->createMock(PasswordLostProducer::class);
        $producer
            ->expects($this->once())
            ->method('execute')
            ->with($userInDatabase)
        ;

        $askPasswordLost = new AskPasswordLost(
            $manager,
            $producer,
            new NullLogger()
        );

        $askPasswordLost->execute($user);

        $this->assertFalse($userInDatabase->isEnabled());
    }
}
