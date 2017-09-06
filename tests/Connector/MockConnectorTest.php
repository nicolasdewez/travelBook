<?php

namespace App\Tests\Connector;

use App\Connector\MockConnector;
use App\Model\ResultPlace;
use PHPUnit\Framework\TestCase;

class MockConnectorTest extends TestCase
{
    public function testSearchPlaces()
    {
        $expected  = [
            (new ResultPlace())
                ->setTitle('title')
                ->setLocale('en')
                ->setLatitude(50.871091000000)
                ->setLongitude(1.583382000000)
                ->setLinkShow('<a>Show</a>')
        ];

        $connector = new MockConnector();
        $this->assertEquals($expected, $connector->searchPlaces('query', 'en'));
    }
}
