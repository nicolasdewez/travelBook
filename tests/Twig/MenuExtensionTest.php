<?php

namespace App\Tests\Twig;

use App\Builder\MenuBuilder;
use App\Entity\User;
use App\Renderer\MenuRenderer;
use App\Twig\MenuExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twig\TwigFunction;

class MenuExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $menuExtension = new MenuExtension(
            $this->createMock(TokenStorageInterface::class),
            $this->createMock(MenuBuilder::class),
            $this->createMock(MenuRenderer::class)
        );

        $this->assertEquals(
            [new TwigFunction('display_menu', [$menuExtension, 'displayMenu'])],
            $menuExtension->getFunctions()
        );
    }

    public function testDisplayMenuUserNotConnected()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->withAnyParameters()
            ->willReturn(null)
        ;

        $menuExtension = new MenuExtension(
            $tokenStorage,
            $this->createMock(MenuBuilder::class),
            $this->createMock(MenuRenderer::class)
        );

        $this->assertSame('', $menuExtension->displayMenu());
    }

    public function testDisplayMenu()
    {
        $user = (new User())->setRoles(['role1']);

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->exactly(3))
            ->method('getUser')
            ->withAnyParameters()
            ->willReturn($user)
        ;

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->exactly(3))
            ->method('getToken')
            ->withAnyParameters()
            ->willReturn($token)
        ;

        $builder = $this->createMock(MenuBuilder::class);
        $builder
            ->expects($this->once())
            ->method('execute')
            ->with(['role1'])
            ->willReturn(['item1', 'item2'])
        ;

        $renderer = $this->createMock(MenuRenderer::class);
        $renderer
            ->expects($this->once())
            ->method('execute')
            ->with(['item1', 'item2'])
            ->willReturn('content')
        ;

        $menuExtension = new MenuExtension($tokenStorage, $builder, $renderer);
        $this->assertSame('content', $menuExtension->displayMenu());
    }

    public function testIsUserConnected()
    {
        $user = new User();

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->withAnyParameters()
            ->willReturn($user)
        ;

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->withAnyParameters()
            ->willReturn($token)
        ;

        $menuExtension = new MenuExtension(
            $tokenStorage,
            $this->createMock(MenuBuilder::class),
            $this->createMock(MenuRenderer::class)
        );

        $class = new \ReflectionClass($menuExtension);
        $method = $class->getMethod('isUserConnected');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($menuExtension, []));
    }

    public function testIsUserUserInvalid()
    {
        $user = $this->createMock(MenuBuilder::class);

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->withAnyParameters()
            ->willReturn($user)
        ;

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->withAnyParameters()
            ->willReturn($token)
        ;

        $menuExtension = new MenuExtension(
            $tokenStorage,
            $this->createMock(MenuBuilder::class),
            $this->createMock(MenuRenderer::class)
        );

        $class = new \ReflectionClass($menuExtension);
        $method = $class->getMethod('isUserConnected');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($menuExtension, []));
    }

    public function testIsUserConnectedNoToken()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->withAnyParameters()
            ->willReturn(null)
        ;

        $menuExtension = new MenuExtension(
            $tokenStorage,
            $this->createMock(MenuBuilder::class),
            $this->createMock(MenuRenderer::class)
        );

        $class = new \ReflectionClass($menuExtension);
        $method = $class->getMethod('isUserConnected');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($menuExtension, []));
    }

    public function testGetRolesUser()
    {
        $user = (new User())->setRoles(['role1', 'role2']);

        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->exactly(2))
            ->method('getUser')
            ->withAnyParameters()
            ->willReturn($user)
        ;

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->exactly(2))
            ->method('getToken')
            ->withAnyParameters()
            ->willReturn($token)
        ;

        $menuExtension = new MenuExtension(
            $tokenStorage,
            $this->createMock(MenuBuilder::class),
            $this->createMock(MenuRenderer::class)
        );

        $class = new \ReflectionClass($menuExtension);
        $method = $class->getMethod('getRolesUser');
        $method->setAccessible(true);

        $this->assertSame(['role1', 'role2'], $method->invokeArgs($menuExtension, []));
    }

    public function testGetRolesUserNotConnected()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->withAnyParameters()
            ->willReturn(null)
        ;

        $menuExtension = new MenuExtension(
            $tokenStorage,
            $this->createMock(MenuBuilder::class),
            $this->createMock(MenuRenderer::class)
        );

        $class = new \ReflectionClass($menuExtension);
        $method = $class->getMethod('getRolesUser');
        $method->setAccessible(true);

        $this->assertSame([], $method->invokeArgs($menuExtension, []));
    }
}
