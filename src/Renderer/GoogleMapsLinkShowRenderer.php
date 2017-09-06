<?php

namespace App\Renderer;

use Twig\Environment as Twig;

class GoogleMapsLinkShowRenderer implements LinkShowRendererInterface
{
    const BASE_URL = 'https://www.google.fr/maps/place';

    /** @var Twig */
    private $twig;

    /**
     * @param Twig $twig
     */
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(float $latitude, float $longitude): string
    {
        return $this->twig->render('renderer/link-show.html.twig', [
            'baseUrl' => self::BASE_URL,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }
}
