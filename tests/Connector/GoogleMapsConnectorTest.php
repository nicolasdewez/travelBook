<?php

namespace App\Tests\Connector;

use App\Connector\GoogleMapsConnector;
use App\Model\ResultPlace;
use App\Renderer\GoogleMapsLinkShowRenderer;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GoogleMapsConnectorTest extends TestCase
{
    public function testSearchPlaces()
    {
        $body = '{"results":[{"geometry":{"location":{"lat":12,"lng":14}},"name":"title"}]}';
        $response = new Response(200, [], $body);

        $client = $this->createMock(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with('get', [
                'place/textsearch/json', [
                    'query' => [
                        'query' => 'query',
                        'language' => 'en',
                        'key' => 'key',
                    ],
                ]
            ])
            ->willReturn($response)
        ;

        $renderer = $this->createMock(GoogleMapsLinkShowRenderer::class);
        $renderer
            ->expects($this->once())
            ->method('execute')
            ->with(12, 14)
            ->willReturn('link')
        ;

        $connector = new GoogleMapsConnector($client, $renderer, 'key');

        $expected = [
            (new ResultPlace())
                ->setTitle('title')
                ->setLocale('en')
                ->setLatitude(12)
                ->setLongitude(14)
                ->setLinkShow('link')
        ];

        $this->assertEquals($expected, $connector->searchPlaces('query', 'en'));
    }
}
