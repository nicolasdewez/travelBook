<?php

namespace App\Controller\Call;

use App\Entity\Feedback;
use App\Feedback\ProcessFeedback;
use App\Manager\FeedbackManager;
use App\Manager\FilterTypeManager;
use App\Pagination\InformationPagination;
use App\Session\Flash;
use App\Session\FlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment as Twig;

/**
 * @Route("/call/feedback")
 * @Security("has_role('ROLE_CALLER')")
 */
class FeedbackController
{
    const REDIRECT_LIST = 'list';
    const REDIRECT_PROCESSED = 'processed';

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
     * @param int                   $page
     * @param Request               $request
     * @param FeedbackManager       $manager
     * @param FilterTypeManager     $filterManager
     * @param InformationPagination $pagination
     *
     * @return Response
     *
     * @Route(
     *     "/list/{page}",
     *     name="app_call_feedback_list",
     *     requirements={"page": "^\d+$"},
     *     defaults={"page": 1},
     *     methods={"GET", "POST"}
     * )
     */
    public function listAction(int $page, Request $request, FeedbackManager $manager, FilterTypeManager $filterManager, InformationPagination $pagination): Response
    {
        $form = $filterManager->executeToListFeedback($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->getListResponse($form->createView(), 'call/feedback/list.html.twig', 0, 0, 0, []);
        }

        $nbElements = $manager->countElements($form->getData());
        $nbPages = $pagination->getNbPages($nbElements) ?: 1;
        if ($nbPages < $page) {
            return new RedirectResponse($this->router->generate('app_call_feedback_list'));
        }

        return $this->getListResponse(
            $form->createView(),
            'call/feedback/list.html.twig',
            $page,
            $nbPages,
            $nbElements,
            $manager->listElements($form->getData(), $page)
        );
    }

    /**
     * @param Feedback        $feedback
     * @param ProcessFeedback $processFeedback
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/process",
     *     name="app_call_feedback_process",
     *     requirements={"id": "^\d+$"},
     *     methods={"GET"}
     * )
     */
    public function processAction(Feedback $feedback, ProcessFeedback $processFeedback): Response
    {
        if ($feedback->isProcessed()) {
            throw new AccessDeniedException(sprintf('Process feedback is not possible for id %d.', $feedback->getId()));
        }

        $processFeedback->execute($feedback);

        $this->flashMessage->add(Flash::TYPE_NOTICE, 'call.feedback.list.process_feedback_ok');

        return new RedirectResponse($this->router->generate('app_call_feedback_list'));
    }

    /**
     * @param FormView $form
     * @param string   $view
     * @param int      $page
     * @param int      $nbPages
     * @param int      $nbElements
     * @param array    $elements
     *
     * @return Response
     */
    private function getListResponse(FormView $form, string $view, int $page, int $nbPages, int $nbElements, array $elements): Response
    {
        return new Response(
            $this->twig->render($view, [
                'form' => $form,
                'page' => $page,
                'nbPages' => $nbPages,
                'nbElements' => $nbElements,
                'elements' => $elements,
            ])
        );
    }
}
