<?php

namespace App\Tests\Renderer;

use App\Renderer\MenuRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment as Twig;

class MenuRendererTest extends TestCase
{
    public function testExecute()
    {
        $items = ['item1', 'item2'];

        $twig = $this->createMock(Twig::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('common/menu.html.twig', ['items' => $items])
            ->willReturn('content')
        ;

        $renderer = new MenuRenderer($twig);
        $this->assertSame('content', $renderer->execute($items));
    }
}
