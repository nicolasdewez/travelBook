<?php

namespace App\Tests\Finder;

use App\Connector\PlaceConnectorInterface;
use App\Finder\PlaceFinder;
use PHPUnit\Framework\TestCase;

class PlaceFinderTest extends TestCase
{
    public function testSearch()
    {
        $results = ['place1', 'place2'];

        $connector = $this->createMock(PlaceConnectorInterface::class);
        $connector
            ->expects($this->once())
            ->method('searchPlaces')
            ->with('query', 'en')
            ->willReturn($results)
        ;

        $finder = new PlaceFinder($connector);
        $this->assertSame($results, $finder->search('query', 'en'));
    }
}
