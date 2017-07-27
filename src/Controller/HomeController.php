<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as Twig;

class HomeController
{
    /**
     * @param Twig $twig
     *
     * @return Response
     *
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function indexAction(Twig $twig): Response
    {
        return new Response(
            $twig->render('home/index.html.twig')
        );
    }
}
