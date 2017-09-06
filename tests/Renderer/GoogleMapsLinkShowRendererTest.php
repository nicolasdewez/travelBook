<?php

namespace App\Tests\Renderer;

use App\Renderer\GoogleMapsLinkShowRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment as Twig;

class GoogleMapsLinkShowRendererTest extends TestCase
{
    public function testExecute()
    {
        $twig = $this->createMock(Twig::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('renderer/link-show.html.twig', [
                'baseUrl' => GoogleMapsLinkShowRenderer::BASE_URL,
                'latitude' => 12,
                'longitude' => 14,
            ])
            ->willReturn('content')
        ;

        $renderer = new GoogleMapsLinkShowRenderer($twig);
        $this->assertSame('content', $renderer->execute(12, 14));
    }
}
