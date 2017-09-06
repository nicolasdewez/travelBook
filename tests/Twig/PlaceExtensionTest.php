<?php

namespace App\Tests\Twig;

use App\Entity\Place;
use App\Renderer\LinkShowRendererInterface as Renderer;
use App\Twig\PlaceExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class PlaceExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $placeExtension = new PlaceExtension(
            $this->createMock(Renderer::class)
        );

        $this->assertEquals(
            [new TwigFunction('place_link_show', [$placeExtension, 'getLinkShow'])],
            $placeExtension->getFunctions()
        );
    }

    public function testGetLnkShow()
    {
        $renderer = $this->createMock(Renderer::class);
        $renderer
            ->expects($this->once())
            ->method('execute')
            ->with(12, 14)
            ->willReturn('content')
        ;

        $place = (new Place())
            ->setLatitude(12)
            ->setLongitude(14)
        ;

        $placeExtension = new PlaceExtension($renderer);
        $this->assertSame('content', $placeExtension->getLinkShow($place));
    }
}
