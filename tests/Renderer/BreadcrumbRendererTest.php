<?php

namespace App\Tests\Renderer;

use App\Renderer\BreadcrumbRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment as Twig;

class BreadcrumbRendererTest extends TestCase
{
    public function testExecute()
    {
        $items = ['item1', 'item2'];

        $twig = $this->createMock(Twig::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('common/breadcrumb.html.twig', ['items' => $items])
            ->willReturn('content')
        ;

        $renderer = new BreadcrumbRenderer($twig);
        $this->assertSame('content', $renderer->execute($items));
    }
}
