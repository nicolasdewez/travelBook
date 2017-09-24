<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Manager\PictureManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment as Twig;

/**
 * @Route("/pictures")
 */
class PictureController
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
     * @param UserInterface  $user
     * @param PictureManager $manager
     *
     * @return Response
     *
     * @Route("", name="app_pictures", methods={"GET"})
     */
    public function myTravelsAction(UserInterface $user, PictureManager $manager): Response
    {
        return new Response($this->twig->render('picture/my-pictures.html.twig', ['elements' => $manager->listByUser($user)]));
    }
}
