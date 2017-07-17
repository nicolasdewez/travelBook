<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as Twig;

class HomeController
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
     * @Route("/", name="app_home")
     */
    public function indexAction(): Response
    {
        return new Response($this->twig->render('home/index.html.twig'));
    }
}
