<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Travel;
use App\Form\Type\PictureType;
use App\Form\Type\TravelType;
use App\Manager\PictureManager;
use App\Manager\TravelManager;
use App\Producer\AnalyzePictureProducer;
use App\Session\Flash;
use App\Session\FlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment as Twig;

/**
 * @Route("/travels")
 */
class TravelController
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var Twig */
    private $twig;

    /** @var RouterInterface */
    private $router;

    /** @var FlashMessage */
    private $flashMessage;

    /**
     * @param FormFactoryInterface $formFactory
     * @param Twig                 $twig
     * @param RouterInterface      $router
     * @param FlashMessage         $flashMessage
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        Twig $twig,
        RouterInterface $router,
        FlashMessage $flashMessage
    ) {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->flashMessage = $flashMessage;
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

    /**
     * @param Request       $request
     * @param UserInterface $user
     * @param TravelManager $manager
     *
     * @return Response
     *
     * @Route("/create", name="app_travels_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, UserInterface $user, TravelManager $manager): Response
    {
        $form = $this->formFactory->create(TravelType::class, (new Travel())->setUser($user));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'travels.create.ok');

            return new RedirectResponse($this->router->generate('app_travels_add_pictures', ['id' => $form->getData()->getId()]));
        }

        return new Response(
            $this->twig->render('travel/create.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param Travel                 $travel
     * @param Request                $request
     * @param PictureManager         $manager
     * @param AnalyzePictureProducer $producer
     *
     * @return Response
     *
     * @Route("/{id}/add-pictures", name="app_travels_add_pictures", methods={"GET", "POST"})
     */
    public function addPicturesAction(Travel $travel, Request $request, PictureManager $manager, AnalyzePictureProducer $producer): Response
    {
        $form = $this->formFactory->create(PictureType::class, (new Picture())->setTravel($travel));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());
            $producer->execute($form->getData());
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'travels.add_pictures.ok');

            return new RedirectResponse($this->router->generate('app_travels_add_pictures', ['id' => $travel->getId()]));
        }

        return new Response(
            $this->twig->render('travel/add-pictures.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
