<?php

namespace App\Connector;

use App\Model\ResultPlace;

interface PlaceConnectorInterface
{
    /**
     * @param string $query
     * @param string $locale
     *
     * @return ResultPlace[]
     */
    public function searchPlaces(string $query, string $locale): array;
}
