<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\CheckRegistrationCode;
use App\Security\RegistrationCode;
use App\Workflow\RegistrationWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CheckRegistrationCodeTest extends TestCase
{
    public function testExecuteUserNotFound()
    {
        $code =  '';

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn(null)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $workflow = $this->createMock(RegistrationWorkflow::class);
        $workflow
            ->expects($this->never())
            ->method('canApplyActive')
        ;

        $checkRegistrationCode = new CheckRegistrationCode($manager, $workflow, new NullLogger(), 'secret');
        $this->assertFalse($checkRegistrationCode->execute($code));
        $this->assertNull($checkRegistrationCode->getUser());
    }

    public function testExecuteUsernameInvalid()
    {
        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $code = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret')));

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn((new User())->setUsername('username'))
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $workflow = $this->createMock(RegistrationWorkflow::class);
        $workflow
            ->expects($this->never())
            ->method('canApplyActive')
        ;

        $checkRegistrationCode = new CheckRegistrationCode($manager, $workflow, new NullLogger(), 'secret');
        $this->assertFalse($checkRegistrationCode->execute($code));
        $this->assertNull($checkRegistrationCode->getUser());
    }

    public function testExecuteMd5Invalid()
    {
        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $code = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret2')));

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn((new User())->setUsername('ndewez'))
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $workflow = $this->createMock(RegistrationWorkflow::class);
        $workflow
            ->expects($this->never())
            ->method('canApplyActive')
        ;

        $checkRegistrationCode = new CheckRegistrationCode($manager, $workflow, new NullLogger(), 'secret');
        $this->assertFalse($checkRegistrationCode->execute($code));
        $this->assertNull($checkRegistrationCode->getUser());
    }

    public function testExecuteTimestampInvalid()
    {
        $validity = new \DateTime('2017-01-01');
        $code = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret')));

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn((new User())->setUsername('ndewez'))
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $workflow = $this->createMock(RegistrationWorkflow::class);
        $workflow
            ->expects($this->never())
            ->method('canApplyActive')
        ;

        $checkRegistrationCode = new CheckRegistrationCode($manager, $workflow, new NullLogger(), 'secret');
        $this->assertFalse($checkRegistrationCode->execute($code));
        $this->assertNull($checkRegistrationCode->getUser());
    }

    public function testExecuteRegistrationNotInProgress()
    {
        $user = (new User())->setUsername('ndewez');

        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $code = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret')));

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn($user)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $workflow = $this->createMock(RegistrationWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyActive')
            ->with($user)
            ->willReturn(false)
        ;

        $checkRegistrationCode = new CheckRegistrationCode($manager, $workflow, new NullLogger(), 'secret');
        $this->assertFalse($checkRegistrationCode->execute($code));
        $this->assertNull($checkRegistrationCode->getUser());
    }

    public function testExecuteOk()
    {
        $user = (new User())->setUsername('ndewez');

        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $code = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret')));

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn($user)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository)
        ;

        $workflow = $this->createMock(RegistrationWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('canApplyActive')
            ->with($user)
            ->willReturn(true)
        ;

        $checkRegistrationCode = new CheckRegistrationCode($manager, $workflow, new NullLogger(), 'secret');
        $this->assertTrue($checkRegistrationCode->execute($code));
        $this->assertSame($user, $checkRegistrationCode->getUser());
    }
}
