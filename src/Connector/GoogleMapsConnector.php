<?php

namespace App\Connector;

use App\Model\ResultPlace;
use App\Renderer\GoogleMapsLinkShowRenderer;
use GuzzleHttp\Client;

class GoogleMapsConnector implements PlaceConnectorInterface
{
    /** @var Client */
    private $client;

    /** @var string */
    private $apiKey;

    /** @var GoogleMapsLinkShowRenderer */
    private $linkShowRenderer;

    /**
     * @param Client                     $client
     * @param GoogleMapsLinkShowRenderer $linkShowRenderer
     * @param string                     $apiKey
     */
    public function __construct(Client $client, GoogleMapsLinkShowRenderer $linkShowRenderer, string $apiKey)
    {
        $this->client = $client;
        $this->linkShowRenderer = $linkShowRenderer;
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function searchPlaces(string $query, string $locale): array
    {
        $results = [];

        $response = $this->client->get('place/textsearch/json', [
            'query' => [
                'query' => $query,
                'language' => $locale,
                'key' => $this->apiKey,
            ],
        ]);

        $response = json_decode((string) $response->getBody(), true);
        foreach ($response['results'] as $result) {
            $location = $result['geometry']['location'];

            $results[] = (new ResultPlace())
                ->setTitle($result['name'])
                ->setLocale($locale)
                ->setLatitude($location['lat'])
                ->setLongitude($location['lng'])
                ->setLinkShow($this->linkShowRenderer->execute($location['lat'], $location['lng']))
            ;
        }

        return $results;
    }
}
