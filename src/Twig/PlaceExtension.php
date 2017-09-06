<?php

namespace App\Twig;

use App\Entity\Place;
use App\Renderer\LinkShowRendererInterface as Renderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PlaceExtension extends AbstractExtension
{
    /** @var Renderer */
    private $renderer;

    /**
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('place_link_show', [$this, 'getLinkShow']),
        ];
    }

    /**
     * @param Place $place
     *
     * @return string
     */
    public function getLinkShow(Place $place): string
    {
        return $this->renderer->execute($place->getLatitude(), $place->getLongitude());
    }
}
