<?php

namespace App\Controller\Validation;

use App\Checker\InvalidatePicture;
use App\Checker\ValidatePicture;
use App\Entity\InvalidationPicture;
use App\Entity\Picture;
use App\Form\Type\InvalidationPictureType;
use App\Manager\FilterTypeManager;
use App\Manager\PictureManager;
use App\Pagination\InformationPagination;
use App\Session\Flash;
use App\Session\FlashMessage;
use App\Workflow\CheckPictureWorkflow;
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
 * @Route("/validation/pictures")
 * @Security("has_role('ROLE_VALIDATOR')")
 */
class PictureController
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
     * @param PictureManager        $manager
     * @param FilterTypeManager     $filterManager
     * @param InformationPagination $pagination
     *
     * @return Response
     *
     * @Route(
     *     "/list/{page}",
     *     name="app_validation_pictures_list",
     *     requirements={"page": "^\d+$"},
     *     defaults={"page": 1},
     *     methods={"GET", "POST"}
     * )
     */
    public function listAction(int $page, Request $request, PictureManager $manager, FilterTypeManager $filterManager, InformationPagination $pagination): Response
    {
        $form = $filterManager->executeToValidatePictures($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->getListResponse($form->createView(), 'validation/picture/list.html.twig', 0, 0, 0, []);
        }

        $nbElements = $manager->countToValidationElements($form->getData());
        $nbPages = $pagination->getNbPages($nbElements) ?: 1;
        if ($nbPages < $page) {
            return new RedirectResponse($this->router->generate('app_validation_pictures_list'));
        }

        return $this->getListResponse(
            $form->createView(),
            'validation/picture/list.html.twig',
            $page,
            $nbPages,
            $nbElements,
            $manager->listToValidationElements($form->getData(), $page)
        );
    }

    /**
     * @param Picture              $picture
     * @param string               $redirect
     * @param CheckPictureWorkflow $checkPictureWorkflow
     * @param ValidatePicture      $validatePicture
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/validation/{redirect}",
     *     name="app_validation_pictures_validation",
     *     requirements={"id": "^\d+$"},
     *     defaults={"redirect": "list"},
     *     methods={"GET"}
     * )
     */
    public function validationAction(Picture $picture, string $redirect, CheckPictureWorkflow $checkPictureWorkflow, ValidatePicture $validatePicture): Response
    {
        if (!$checkPictureWorkflow->canApplyValidation($picture)) {
            throw new AccessDeniedException(sprintf('Check picture\'s workflow is not valid for execute this action to picture %s.', $picture->getId()));
        }

        $validatePicture->execute($picture);

        $this->flashMessage->add(Flash::TYPE_NOTICE, 'validation.pictures.list.validation_ok');

        return new RedirectResponse($this->router->generate($this->getRouteToRedirect($redirect)));
    }

    /**
     * @param Picture              $picture
     * @param string               $redirect
     * @param Request              $request
     * @param CheckPictureWorkflow $checkPictureWorkflow
     * @param InvalidatePicture    $invalidatePicture
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/invalidation/{redirect}",
     *     name="app_validation_pictures_invalidation",
     *     requirements={"id": "^\d+$", "redirect": "^[a-z]+$"},
     *     defaults={"redirect": "list"},
     *     methods={"GET", "POST"}
     * )
     */
    public function invalidationAction(Picture $picture, string $redirect, Request $request, CheckPictureWorkflow $checkPictureWorkflow, InvalidatePicture $invalidatePicture): Response
    {
        if (!$checkPictureWorkflow->canApplyInvalidation($picture)) {
            throw new AccessDeniedException(sprintf('Check picture\'s workflow is not valid for execute this action to picture %s.', $picture->getId()));
        }

        $form = $this->formFactory->create(InvalidationPictureType::class, (new InvalidationPicture())->setPicture($picture));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invalidatePicture->execute($form->getData());
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'validation.pictures.invalidation.invalidation_ok');

            return new RedirectResponse($this->router->generate($this->getRouteToRedirect($redirect)));
        }

        return new Response(
            $this->twig->render('validation/picture/invalidation.html.twig', [
                'form' => $form->createView(),
                'picture' => $picture,
                'redirect' => $redirect,
            ])
        );
    }

    /**
     * @param Picture              $picture
     * @param CheckPictureWorkflow $checkPictureWorkflow
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/re-validation",
     *     name="app_validation_pictures_re_validation",
     *     requirements={"id": "^\d+$"},
     *     methods={"GET"}
     * )
     */
    public function reValidationAction(Picture $picture, CheckPictureWorkflow $checkPictureWorkflow): Response
    {
        if (!$checkPictureWorkflow->canApplyValidation($picture)) {
            throw new AccessDeniedException(sprintf('Check picture\'s workflow is not valid for execute this action to picture %s.', $picture->getId()));
        }

        return new Response(
            $this->twig->render('validation/picture/re-validation.html.twig', [
                'picture' => $picture,
            ])
        );
    }

    /**
     * @param int                   $page
     * @param Request               $request
     * @param PictureManager        $manager
     * @param FilterTypeManager     $filterManager
     * @param InformationPagination $pagination
     *
     * @return Response
     *
     * @Route(
     *     "/list-processed/{page}",
     *     name="app_validation_pictures_list_processed",
     *     requirements={"page": "^\d+$"},
     *     defaults={"page": 1},
     *     methods={"GET", "POST"}
     * )
     */
    public function listProcessedAction(int $page, Request $request, PictureManager $manager, FilterTypeManager $filterManager, InformationPagination $pagination): Response
    {
        $form = $filterManager->executeToReValidatePictures($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->getListResponse($form->createView(), 'validation/picture/list-processed.html.twig', 0, 0, 0, []);
        }

        $nbElements = $manager->countToReValidationElements($form->getData());
        $nbPages = $pagination->getNbPages($nbElements) ?: 1;
        if ($nbPages < $page) {
            return new RedirectResponse($this->router->generate('app_validation_pictures_list_processed'));
        }

        return $this->getListResponse(
            $form->createView(),
            'validation/picture/list-processed.html.twig',
            $page,
            $nbPages,
            $nbElements,
            $manager->listToReValidationElements($form->getData(), $page)
        );
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

    /**
     * @param string $code
     *
     * @return string
     */
    private function getRouteToRedirect(string $code): string
    {
        switch ($code) {
            case self::REDIRECT_LIST: return 'app_validation_pictures_list';
            case self::REDIRECT_PROCESSED: return 'app_validation_pictures_list_processed';
        }

        return 'app_validation_pictures_list';
    }
}
