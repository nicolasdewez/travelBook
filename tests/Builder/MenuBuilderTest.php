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
    public function testExecuteUser()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
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
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5');
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
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
            new MenuItem('title6', '', false, [
                new MenuItem('title4', 'route4', false),
                new MenuItem('title5', 'route5', false),
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
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5');
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
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
            new MenuItem('title6', '', false, [
                new MenuItem('title4', 'route4', false),
                new MenuItem('title5', 'route5', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::VALIDATOR]));
    }

    public function testExecuteCaller()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
            new MenuItem('title5', '', false, [
                new MenuItem('title4', 'route4', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::CALLER]));
    }

    public function testExecuteAdminAndValidator()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5', 'route6', 'route7');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5', 'title6', 'title7', 'title8', 'title9');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
            new MenuItem('title6', '', false, [
                new MenuItem('title4', 'route4', false),
                new MenuItem('title5', 'route5', false),
            ]),
            new MenuItem('title9', '', false, [
                new MenuItem('title7', 'route6', false),
                new MenuItem('title8', 'route7', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::ADMIN, Role::VALIDATOR]));
    }

    public function testExecuteAdminAndCaller()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5', 'route6');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5', 'title6', 'title7', 'title8');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
            new MenuItem('title6', '', false, [
                new MenuItem('title4', 'route4', false),
                new MenuItem('title5', 'route5', false),
            ]),
            new MenuItem('title8', '', false, [
                new MenuItem('title7', 'route6', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::ADMIN, Role::CALLER]));
    }

    public function testExecuteValidatorAndCaller()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5', 'route6');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5', 'title6', 'title7', 'title8');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
            new MenuItem('title6', '', false, [
                new MenuItem('title4', 'route4', false),
                new MenuItem('title5', 'route5', false),
            ]),
            new MenuItem('title8', '', false, [
                new MenuItem('title7', 'route6', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::ADMIN, Role::CALLER]));
    }

    public function testExecuteAdminValidatorAndCaller()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2', 'route3', 'route4', 'route5', 'route6', 'route7', 'route8');
        $router->method('match')->willReturn(['_route' => 'currentRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2','title3', 'title4', 'title5', 'title6', 'title7', 'title8', 'title9', 'title10', 'title11');

        $builder = new MenuBuilder(
            $request,
            $router,
            $translator
        );

        $expected = [
            new MenuItem('title1', 'route1', false),
            new MenuItem('title2', 'route2', false),
            new MenuItem('title3', 'route3', false),
            new MenuItem('title6', '', false, [
                new MenuItem('title4', 'route4', false),
                new MenuItem('title5', 'route5', false),
            ]),
            new MenuItem('title9', '', false, [
                new MenuItem('title7', 'route6', false),
                new MenuItem('title8', 'route7', false),
            ]),
            new MenuItem('title11', '', false, [
                new MenuItem('title10', 'route8', false),
            ]),
        ];

        $this->assertEquals($expected, $builder->execute([Role::ADMIN, Role::VALIDATOR, Role::CALLER]));
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

    public function testIsCallUser()
    {
        $builder = new MenuBuilder(
            $this->createMock(RequestStack::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(TranslatorInterface::class)
        );

        $class = new \ReflectionClass($builder);
        $method = $class->getMethod('isCallerUser');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($builder, [[Role::CALLER]]));
    $this->assertTrue($method->invokeArgs($builder, [[Role::ADMIN, Role::CALLER]]));
        $this->assertFalse($method->invokeArgs($builder, [[Role::USER]]));
    }
}
