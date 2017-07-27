<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as Twig;

/**
 * @Route("/travels")
 */
class TravelController
{
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
     * @return Response
     *
     * @Route("", name="app_travels", methods={"GET"})
     */
    public function myTravelsAction(): Response
    {
        return new Response($this->twig->render('travel/my-travels.html.twig'));
    }
}
