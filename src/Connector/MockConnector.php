<?php

namespace App\Connector;

use App\Model\ResultPlace;

class MockConnector implements PlaceConnectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function searchPlaces(string $query, string $locale): array
    {
        return [
            (new ResultPlace())
                ->setTitle('title')
                ->setLocale($locale)
                ->setLatitude(50.871091000000)
                ->setLongitude(1.583382000000)
                ->setLinkShow('<a>Show</a>'),
        ];
    }
}
