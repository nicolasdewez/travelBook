<?php

namespace App\Tests\Twig;

use App\Builder\BreadcrumbBuilder;
use App\Renderer\BreadcrumbRenderer;
use App\Twig\BreadcrumbExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class BreadcrumbExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $breadcrumbExtension = new BreadcrumbExtension(
            $this->createMock(BreadcrumbBuilder::class),
            $this->createMock(BreadcrumbRenderer::class)
        );

        $this->assertEquals(
            [new TwigFunction('display_breadcrumb', [$breadcrumbExtension, 'displayBreadcrumb'])],
            $breadcrumbExtension->getFunctions()
        );
    }

    public function testDisplayBreadcrumb()
    {
        $items = ['item1', 'item2'];

        $builder = $this->createMock(BreadcrumbBuilder::class);
        $builder
            ->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn($items)
        ;

        $renderer = $this->createMock(BreadcrumbRenderer::class);
        $renderer
            ->expects($this->once())
            ->method('execute')
            ->with($items)
            ->willReturn('content')
        ;

        $breadcrumbExtension = new BreadcrumbExtension($builder, $renderer);
        $this->assertSame('content', $breadcrumbExtension->displayBreadcrumb());
    }
}
