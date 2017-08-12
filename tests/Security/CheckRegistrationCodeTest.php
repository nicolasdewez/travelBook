<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\CheckRegistrationCode;
use App\Security\RegistrationCode;
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

        $checkRegistrationCode = new CheckRegistrationCode(
            $repository,
            new NullLogger(),
            'secret'
        );

        $this->assertFalse($checkRegistrationCode->execute($code));
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

        $checkRegistrationCode = new CheckRegistrationCode(
            $repository,
            new NullLogger(),
            'secret'
        );

        $this->assertFalse($checkRegistrationCode->execute($code));
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

        $checkRegistrationCode = new CheckRegistrationCode(
            $repository,
            new NullLogger(),
            'secret'
        );

        $this->assertFalse($checkRegistrationCode->execute($code));
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

        $checkRegistrationCode = new CheckRegistrationCode(
            $repository,
            new NullLogger(),
            'secret'
        );

        $this->assertFalse($checkRegistrationCode->execute($code));
    }

    public function testExecuteRegistrationNotInProgress()
    {
        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $code = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret')));

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn(
                (new User())
                    ->setUsername('ndewez')
                    ->setRegistrationInProgress(false)
            )
        ;

        $checkRegistrationCode = new CheckRegistrationCode(
            $repository,
            new NullLogger(),
            'secret'
        );

        $this->assertFalse($checkRegistrationCode->execute($code));
    }

    public function testExecuteOk()
    {
        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $code = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret')));

        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationCode' => $code])
            ->willReturn(
                (new User())
                    ->setUsername('ndewez')
                    ->setRegistrationInProgress(true)
            )
        ;

        $checkRegistrationCode = new CheckRegistrationCode(
            $repository,
            new NullLogger(),
            'secret'
        );

        $this->assertTrue($checkRegistrationCode->execute($code));
    }
}