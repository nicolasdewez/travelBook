<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Form\Type\TravelType;
use App\Manager\TravelManager;
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
     * @Route("/create", name="app_travels_create_step1", methods={"GET", "POST"})
     */
    public function createStep1Action(Request $request, UserInterface $user, TravelManager $manager): Response
    {
        $form = $this->formFactory->create(TravelType::class, (new Travel())->setUser($user));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'travels.step1.ok');

            return new RedirectResponse($this->router->generate('app_travels'));
        }

        return new Response($this->twig->render('travel/create-step1.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
