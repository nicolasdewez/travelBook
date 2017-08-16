<?php

namespace App\Tests\Builder;

use App\Builder\BreadcrumbBuilder;
use App\Model\BreadcrumbItem;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BreadcrumbBuilderTest extends TestCase
{
    public function testExecuteCurrentRouteNotValid()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest
            ->expects($this->once())
            ->method('getPathInfo')
            ->withAnyParameters()
            ->willReturn('pathinfo')
        ;

        $request = $this->createMock(RequestStack::class);
        $request
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->withAnyParameters()
            ->willReturn($currentRequest)
        ;

        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('match')
            ->with('pathinfo')
            ->willReturn(['_route' => 'invalidRoute'])
        ;

        $builder = new BreadcrumbBuilder(
            $request,
            $router,
            $this->createMock(TranslatorInterface::class),
            new NullLogger(),
            realpath(sprintf('%s/../data/config/breadcrumb.yaml', __DIR__))
        );

        $this->assertEquals([], $builder->execute());
    }

    public function testExecute()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest
            ->expects($this->once())
            ->method('getPathInfo')
            ->withAnyParameters()
            ->willReturn('pathinfo')
        ;

        $request = $this->createMock(RequestStack::class);
        $request
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->withAnyParameters()
            ->willReturn($currentRequest)
        ;

        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('generate')
            ->with('app_home')
            ->willReturn('route2')
        ;
        $router
            ->expects($this->once())
            ->method('match')
            ->with('pathinfo')
            ->willReturn(['_route' => 'app_registration'])
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(['breadcrumb.app_registration', []], ['breadcrumb.app_home', []])
            ->willReturnOnConsecutiveCalls('title1', 'title2')
        ;

        $expected = [
            new BreadcrumbItem('title2', 'route2', false),
            new BreadcrumbItem('title1', '#', true),
        ];

        $builder = new BreadcrumbBuilder(
            $request,
            $router,
            $translator,
            new NullLogger(),
            realpath(sprintf('%s/../data/config/breadcrumb.yaml', __DIR__))
        );

        $this->assertEquals($expected, $builder->execute());
    }

    public function testBuildItem()
    {
        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects($this->once())
            ->method('generate')
            ->with('current', [])
            ->willReturn('route')
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->with('', [])
            ->willReturn('title')
        ;

        $builder = new BreadcrumbBuilder(
            $this->createMock(RequestStack::class),
            $router,
            $translator,
            new NullLogger(),
            '/path/invalid.yaml'
        );

        $class = new \ReflectionClass($builder);
        $method = $class->getMethod('buildItem');
        $method->setAccessible(true);

        $expected = new BreadcrumbItem('title', '#', true);

        $this->assertEquals($expected, $method->invokeArgs($builder, [
            ['title' => '', 'title_params' => [], 'route_params' => []],
            'current',
            true,
        ]));

        $expected = new BreadcrumbItem('title', 'route', false);

        $this->assertEquals($expected, $method->invokeArgs($builder, [
            ['title' => ''],
            'current',
            false,
        ]));
    }

    public function testLoadConfigFileNoFile()
    {
        $builder = new BreadcrumbBuilder(
            $this->createMock(RequestStack::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(TranslatorInterface::class),
            new NullLogger(),
            '/path/invalid.yaml'
        );

        $class = new \ReflectionClass($builder);
        $method = $class->getMethod('loadConfigFile');
        $method->setAccessible(true);

        $this->assertEquals([], $method->invokeArgs($builder, []));
    }

    public function testLoadConfigFile()
    {
        $builder = new BreadcrumbBuilder(
            $this->createMock(RequestStack::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(TranslatorInterface::class),
            new NullLogger(),
            realpath(sprintf('%s/../data/config/breadcrumb.yaml', __DIR__))
        );

        $expected = [
            'app_home' => [
                'title' => 'breadcrumb.app_home',
            ],
            'app_registration' => [
                'title' => 'breadcrumb.app_registration',
                'parent' => 'app_home',
            ],
        ];

        $class = new \ReflectionClass($builder);
        $method = $class->getMethod('loadConfigFile');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($builder, []));
    }
}
