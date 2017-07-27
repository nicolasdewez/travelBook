<?php

namespace App\Tests\Builder;

use App\Builder\MenuBuilder;
use App\Model\MenuItem;
use App\Security\Roles;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilderTest extends TestCase
{
    public function testExecuteNoAdmin()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', '', false),  // Menu not ended
            new MenuItem('title3', '', false),  // Menu not ended
            new MenuItem('title4', '', false),  // Menu not ended
        ];

        $this->assertEquals($expected, $builder->execute([]));
    }

    public function testExecuteAdmin()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5', 'route6');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5', 'title6');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', '', false),  // Menu not ended
            new MenuItem('title3', '', false),  // Menu not ended
            new MenuItem('title4', '', false),  // Menu not ended
            new MenuItem('title6', '', false, [ // Menu not ended
                new MenuItem('title5', '', false) // Menu not ended
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Roles::ROLE_ADMIN]));
    }

    public function testExecuteValidator()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5', 'route6', 'route7');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5', 'title6', 'title7');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', '', false),  // Menu not ended
            new MenuItem('title3', '', false),  // Menu not ended
            new MenuItem('title4', '', false),  // Menu not ended
            new MenuItem('title7', '', false, [ // Menu not ended
                new MenuItem('title5', '', false),  // Menu not ended
                new MenuItem('title6', '', false),  // Menu not ended
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Roles::ROLE_VALIDATOR]));
    }

    public function testBuildItem()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturn('route');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('title');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $class = new \ReflectionClass($builder);
        $method = $class->getMethod('buildItem');
        $method->setAccessible(true);

        $expected = new MenuItem('title', 'route', false);
        $this->assertEquals($expected, $method->invokeArgs($builder, ['title', 'aRoute']));

        $expected = new MenuItem('title', null, false);
        $this->assertEquals($expected, $method->invokeArgs($builder, ['title', '']));

        $expected = new MenuItem('title', 'route', true);
        $this->assertEquals($expected, $method->invokeArgs($builder, ['title', 'currentRoute']));
    }

    public function testIsAdminUser()
    {
        $builder = new MenuBuilder(
            $this->createMock(RequestStack::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(TranslatorInterface::class)
        );

        $class = new \ReflectionClass($builder);
        $method = $class->getMethod('isAdminUser');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($builder, [[Roles::ROLE_ADMIN]]));
        $this->assertTrue($method->invokeArgs($builder, [[Roles::ROLE_VALIDATOR]]));
        $this->assertTrue($method->invokeArgs($builder, [[Roles::ROLE_ADMIN, Roles::ROLE_VALIDATOR]]));
        $this->assertFalse($method->invokeArgs($builder, [[Roles::ROLE_USER]]));
    }

    public function testIsValidatorUser()
    {
        $builder = new MenuBuilder(
            $this->createMock(RequestStack::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(TranslatorInterface::class)
        );

        $class = new \ReflectionClass($builder);
        $method = $class->getMethod('isValidatorUser');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($builder, [[Roles::ROLE_VALIDATOR]]));
        $this->assertTrue($method->invokeArgs($builder, [[Roles::ROLE_ADMIN, Roles::ROLE_VALIDATOR]]));
        $this->assertFalse($method->invokeArgs($builder, [[Roles::ROLE_USER]]));
    }
}
