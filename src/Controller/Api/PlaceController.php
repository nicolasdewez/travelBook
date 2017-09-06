<?php

namespace App\Controller\Api;

use App\Finder\PlaceFinder;
use App\Serializer\Format;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/places")
 */
class PlaceController
{
    /**
     * @param string              $query
     * @param string              $locale
     * @param PlaceFinder         $finder
     * @param SerializerInterface $serializer
     *
     * @return Response
     *
     * @Route("/search/{query}/{locale}", name="api_places_search", methods={"GET"})
     */
    public function searchAction(string $query, string $locale, PlaceFinder $finder, SerializerInterface $serializer): Response
    {
        $results = $finder->search($query, $locale);

        return new JsonResponse(
            $serializer->serialize($results, Format::JSON),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
