<?php

namespace App\Controller;

use App\Form\Type\FeedbackType;
use App\Manager\FeedbackManager;
use App\Session\Flash;
use App\Session\FlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
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
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return new Response(
            $this->twig->render('home/index.html.twig')
        );
    }

    /**
     * @param FormFactoryInterface $formFactory
     * @param Request              $request
     * @param FeedbackManager      $manager
     * @param RouterInterface      $router
     * @param FlashMessage         $flashMessage
     *
     * @return Response
     *
     * @Route("/feedback", name="app_feedback", methods={"GET", "POST"})
     */
    public function feedbackAction(
        FormFactoryInterface $formFactory,
        Request $request,
        FeedbackManager $manager,
        RouterInterface $router,
        FlashMessage $flashMessage
    ): Response {
        $form = $formFactory->create(FeedbackType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());
            $flashMessage->add(Flash::TYPE_NOTICE, 'feedback.save_ok');

            return new RedirectResponse($router->generate('app_home'));
        }

        return new Response(
            $this->twig->render('home/feedback.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
