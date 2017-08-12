<?php

namespace App\Tests\Handler;

use App\Entity\User;
use App\Handler\AuthenticationSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessHandlerTest extends TestCase
{
    public function testOnAuthenticationSuccessInvalidUser()
    {
        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('generate')
            ->with(AuthenticationSuccessHandler::DEFAULT_ROUTE)
            ->willReturn('route')
        ;

        $handler = new AuthenticationSuccessHandler(
            $router,
            new NullLogger()
        );

        $user = $this->createMock(UserInterface::class);
        $user
            ->expects($this->once())
            ->method('getUsername')
            ->withAnyParameters()
            ->willReturn('')
        ;

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->withAnyParameters()
            ->willReturn($user)
        ;

        $expected = new RedirectResponse('route');

        $this->assertEquals($expected, $handler->onAuthenticationSuccess(new Request(), $token));
    }

    public function testOnAuthenticationSuccessNotFirstConnection()
    {
        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('generate')
            ->with(AuthenticationSuccessHandler::DEFAULT_ROUTE)
            ->willReturn('route')
        ;

        $handler = new AuthenticationSuccessHandler(
            $router,
            new NullLogger()
        );

        $user = (new User())->setFirstConnection(false);

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->withAnyParameters()
            ->willReturn($user)
        ;

        $expected = new RedirectResponse('route');

        $this->assertEquals($expected, $handler->onAuthenticationSuccess(new Request(), $token));
    }

    public function testOnAuthenticationSuccessFirstConnection()
    {
        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('generate')
            ->with(AuthenticationSuccessHandler::CHANGE_PASSWORD_ROUTE)
            ->willReturn('route')
        ;

        $handler = new AuthenticationSuccessHandler(
            $router,
            new NullLogger()
        );

        $user = new User();

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->withAnyParameters()
            ->willReturn($user)
        ;

        $expected = new RedirectResponse('route');

        $this->assertEquals($expected, $handler->onAuthenticationSuccess(new Request(), $token));
    }
}
