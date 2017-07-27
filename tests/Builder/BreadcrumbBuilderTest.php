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
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturn('route');
        $router->method('match')->willReturn(['_route' => 'invalidRoute']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('title');

        $builder = new BreadcrumbBuilder(
            $request,
            $router,
            $translator,
            new NullLogger(),
            realpath(sprintf('%s/../data/config/breadcrumb.yaml', __DIR__))
        );

        $this->assertEquals([], $builder->execute());
    }

    public function testExecute()
    {
        $currentRequest = $this->createMock(Request::class);
        $currentRequest->method('getPathInfo')->willReturn('');

        $request = $this->createMock(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn($currentRequest);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->willReturnOnConsecutiveCalls('route1', 'route2');
        $router->method('match')->willReturn(['_route' => 'app_registration']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnOnConsecutiveCalls('title1', 'title2');

        $expected = [
            new BreadcrumbItem('title2', 'route2', false),
            new BreadcrumbItem('title1', 'route1', true),
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
        $router->method('generate')->willReturn('route');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('title');

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

        $expected = new BreadcrumbItem('title', 'route', true);

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
