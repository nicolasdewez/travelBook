<?php

namespace App\Tests\Processor;

use App\Entity\User;
use App\Processor\ClientProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ClientProcessorTest extends TestCase
{
    public function testProcessRecordNoRequest()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->never())
            ->method('getToken')
        ;

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
        ;

        $processor = new ClientProcessor($tokenStorage, $requestStack);

        $this->assertSame(
            ['hello', 'extra' => ['field']],
            $processor->processRecord(['hello', 'extra' => ['field']])
        );
    }

    public function testProcessRecordUserNotConnected()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
        ;

        $request = $this->createMock(Request::class);
        $request
            ->expects($this->once())
            ->method('getClientIps')
            ->willReturn(['192.168.0.1'])
        ;

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $processor = new ClientProcessor($tokenStorage, $requestStack);

        $this->assertSame(
            ['hello', 'extra' => ['field', 'ips' => ['192.168.0.1']]],
            $processor->processRecord(['hello', 'extra' => ['field']])
        );
    }

    public function testProcessRecordTokenWithoutUser()
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
        ;

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;

        $request = $this->createMock(Request::class);
        $request
            ->expects($this->once())
            ->method('getClientIps')
            ->willReturn(['192.168.0.1'])
        ;

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $processor = new ClientProcessor($tokenStorage, $requestStack);

        $this->assertSame(
            ['hello', 'extra' => ['field', 'ips' => ['192.168.0.1']]],
            $processor->processRecord(['hello', 'extra' => ['field']])
        );
    }

    public function testProcessRecord()
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new User())
        ;

        $token
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('username')
        ;

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;

        $request = $this->createMock(Request::class);
        $request
            ->expects($this->once())
            ->method('getClientIps')
            ->willReturn(['192.168.0.1'])
        ;

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $processor = new ClientProcessor($tokenStorage, $requestStack);

        $this->assertSame(
            ['hello', 'extra' => ['field', 'ips' => ['192.168.0.1'], 'username' => 'username']],
            $processor->processRecord(['hello', 'extra' => ['field']])
        );
    }
}
