<?php

namespace App\Finder;

use App\Connector\PlaceConnectorInterface;

class PlaceFinder
{
    /** @var PlaceConnectorInterface */
    private $connector;

    /**
     * @param PlaceConnectorInterface $connector
     */
    public function __construct(PlaceConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param string $query
     * @param string $locale
     *
     * @return array
     */
    public function search(string $query, string $locale): array
    {
        // TODO catch Exception
        return $this->connector->searchPlaces($query, $locale);
    }
}
