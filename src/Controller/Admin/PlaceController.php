<?php

namespace App\Controller\Admin;

use App\Entity\Place;
use App\Form\Type\PlaceType;
use App\Manager\FilterTypeManager;
use App\Manager\PlaceManager;
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
use Twig\Environment as Twig;

/**
 * @Route("/admin/places")
 * @Security("has_role('ROLE_ADMIN')")
 */
class PlaceController
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
     * @param int                   $page
     * @param Request               $request
     * @param PlaceManager          $manager
     * @param FilterTypeManager     $filterManager
     * @param InformationPagination $pagination
     *
     * @return Response
     *
     * @Route(
     *     "/list/{page}",
     *     name="app_admin_places_list",
     *     requirements={"page": "^\d+$"},
     *     defaults={"page": 1},
     *     methods={"GET", "POST"}
     * )
     */
    public function listAction(int $page, Request $request, PlaceManager $manager, FilterTypeManager $filterManager, InformationPagination $pagination): Response
    {
        $form = $filterManager->executeToListPlaces($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->getListResponse($form->createView(), 0, 0, 0, []);
        }

        $nbElements = $manager->countElements($form->getData());
        $nbPages = $pagination->getNbPages($nbElements) ?: 1;
        if ($nbPages < $page) {
            return new RedirectResponse($this->router->generate('app_admin_places_list'));
        }

        return $this->getListResponse(
            $form->createView(),
            $page,
            $nbPages,
            $nbElements,
            $manager->listElements($form->getData(), $page)
        );
    }

    /**
     * @param Request      $request
     * @param PlaceManager $manager
     *
     * @return Response
     *
     * @Route("/create", name="app_admin_places_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, PlaceManager $manager): Response
    {
        $form = $this->formFactory->create(PlaceType::class, new Place());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form->getData());
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'admin.places.create.ok');

            return new RedirectResponse($this->router->generate('app_admin_places_list'));
        }

        return new Response(
            $this->twig->render('admin/place/create.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param FormView $form
     * @param int      $page
     * @param int      $nbPages
     * @param int      $nbElements
     * @param array    $elements
     *
     * @return Response
     */
    private function getListResponse(FormView $form, int $page, int $nbPages, int $nbElements, array $elements): Response
    {
        return new Response(
            $this->twig->render('admin/place/list.html.twig', [
                'form' => $form,
                'page' => $page,
                'nbPages' => $nbPages,
                'nbElements' => $nbElements,
                'elements' => $elements,
            ])
        );
    }
}
