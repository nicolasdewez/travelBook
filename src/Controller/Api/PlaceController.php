<?php

namespace App\Controller\Api;

use App\Finder\PlaceFinder;
use App\Manager\PlaceManager;
use App\Serializer\Format;
use App\Serializer\Group;
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
     * @param string              $locale
     * @param string              $query
     * @param PlaceFinder         $finder
     * @param SerializerInterface $serializer
     *
     * @return Response
     *
     * @Route("/search-in-web/{locale}/{query}", name="api_places_search_in_web", requirements={"locale": "^fr|en$"}, methods={"GET"})
     */
    public function searchInWebAction(string $locale, string $query, PlaceFinder $finder, SerializerInterface $serializer): Response
    {
        $results = $finder->search($query, $locale);

        return new JsonResponse(
            $serializer->serialize($results, Format::JSON),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @param string              $locale
     * @param string              $query
     * @param PlaceManager        $manager
     * @param SerializerInterface $serializer
     *
     * @return Response
     *
     * @Route("/search/{locale}/{query}", name="api_places_search", requirements={"locale": "^fr|en$"}, defaults={"query": ""}, methods={"GET"})
     */
    public function searchAction(string $locale, string $query, PlaceManager $manager, SerializerInterface $serializer): Response
    {
        $results = $manager->searchElements($query, $locale);

        return new JsonResponse(
            $serializer->serialize($results, Format::JSON, ['groups' => [Group::ENTITY_PLACE_GET]]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
