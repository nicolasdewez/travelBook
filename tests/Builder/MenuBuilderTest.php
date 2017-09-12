<?php

namespace App\Tests\Builder;

use App\Builder\MenuBuilder;
use App\Model\MenuItem;
use App\Security\Role;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilderTest extends TestCase
{
    public function testExecuteNoAdminAndNoValidator()
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
            new MenuItem('title2', 'route2', false),
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
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', '', false),  // Menu not ended
            new MenuItem('title4', '', false),  // Menu not ended
            new MenuItem('title7', '', false, [
                new MenuItem('title5', 'route3', false),
                new MenuItem('title6', 'route4', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::ADMIN]));
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
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', '', false),  // Menu not ended
            new MenuItem('title4', '', false),  // Menu not ended
            new MenuItem('title7', '', false, [
                new MenuItem('title5', 'route3', false),
                new MenuItem('title6', 'route4', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::VALIDATOR]));
    }

    public function testExecuteAdminAndValidator()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5', 'route6', 'route7', 'route8', 'route9', 'route10');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5', 'title6', 'title7', 'title8', 'title9', 'title10');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', '', false),  // Menu not ended
            new MenuItem('title4', '', false),  // Menu not ended
            new MenuItem('title7', '', false, [
                new MenuItem('title5', 'route3', false),
                new MenuItem('title6', 'route4', false),
            ]),
            new MenuItem('title10', '', false, [
                new MenuItem('title8', 'route5', false),
                new MenuItem('title9', 'route6', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::ADMIN, Role::VALIDATOR]));
    }

    public function testBuildItem()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest
            ->expects($this->exactly(3))
            ->method('getPathInfo')
            ->withAnyParameters()
            ->willReturn('pathinfo')
        ;

        $request = $this->createMock(RequestStack::class);
        $request
            ->expects($this->exactly(3))
            ->method('getCurrentRequest')
            ->withAnyParameters()
            ->willReturn($currentRequest)
        ;

        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->exactly(2))
            ->method('generate')
            ->withConsecutive(['aRoute'], ['currentRoute'])
            ->willReturn('route')
        ;

        $router
            ->expects($this->exactly(3))
            ->method('match')
            ->with('pathinfo')
            ->willReturn(['_route' => 'currentRoute'])
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->exactly(3))
            ->method('trans')
            ->with('title')
            ->willReturn('title')
        ;

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

        $this->assertTrue($method->invokeArgs($builder, [[Role::ADMIN]]));
        $this->assertFalse($method->invokeArgs($builder, [[Role::VALIDATOR]]));
        $this->assertTrue($method->invokeArgs($builder, [[Role::ADMIN, Role::VALIDATOR]]));
        $this->assertFalse($method->invokeArgs($builder, [[Role::USER]]));
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

        $this->assertTrue($method->invokeArgs($builder, [[Role::VALIDATOR]]));
        $this->assertTrue($method->invokeArgs($builder, [[Role::ADMIN, Role::VALIDATOR]]));
        $this->assertFalse($method->invokeArgs($builder, [[Role::USER]]));
    }
}
