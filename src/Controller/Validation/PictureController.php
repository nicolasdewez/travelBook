<?php

namespace App\Controller\Validation;

use App\Checker\InvalidatePicture;
use App\Checker\ValidatePicture;
use App\Entity\InvalidationPicture;
use App\Entity\Picture;
use App\Form\Type\FilterPictureType;
use App\Form\Type\InvalidationPictureType;
use App\Manager\PictureManager;
use App\Pagination\InformationPagination;
use App\Session\FilterStorage;
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
    public function __construct(FormFactoryInterface $formFactory, Twig $twig, RouterInterface $router, FlashMessage $flashMessage)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->flashMessage = $flashMessage;
    }

    /**
     * @param int                   $page
     * @param Request               $request
     * @param PictureManager        $manager
     * @param FilterStorage         $filterStorage
     * @param InformationPagination $pagination
     *
     * @return Response
     *
     * @Route("/list/{page}", name="app_validation_pictures_list", defaults={"page": 1}, methods={"GET", "POST"})
     */
    public function listAction(int $page, Request $request, PictureManager $manager, FilterStorage $filterStorage, InformationPagination $pagination): Response
    {
        $filterPicture = $filterStorage->getFilterPicture();

        $form = $this->formFactory->create(FilterPictureType::class, $filterPicture);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $filterStorage->saveFilterPicture($filterPicture);
            if (!$form->isValid()) {
                return $this->getListResponse($form->createView(), 0, 0, 0, []);
            }
        }

        $nbElements = $manager->countToValidationElements($filterPicture);
        $nbPages = $pagination->getNbPages($nbElements) ?: 1;
        if ($nbPages < $page) {
            return new RedirectResponse($this->router->generate('app_validation_pictures_list'));
        }

        return $this->getListResponse(
            $form->createView(),
            $page,
            $nbPages,
            $nbElements,
            $manager->listToValidationElements($filterPicture, $page)
        );
    }

    /**
     * @param Picture              $picture
     * @param CheckPictureWorkflow $checkPictureWorkflow
     * @param ValidatePicture      $validatePicture
     *
     * @return Response
     *
     * @Route("/{id}/validation", name="app_validation_pictures_validation", methods={"GET"})
     */
    public function validationAction(Picture $picture, CheckPictureWorkflow $checkPictureWorkflow, ValidatePicture $validatePicture): Response
    {
        if (!$checkPictureWorkflow->canApplyValidation($picture)) {
            throw new AccessDeniedException(sprintf('Check picture\'s workflow is not valid for execute this action to picture %s.', $picture->getId()));
        }

        $validatePicture->execute($picture);

        $this->flashMessage->add(Flash::TYPE_NOTICE, 'validation.pictures.list.validation_ok');

        return new RedirectResponse($this->router->generate('app_validation_pictures_list'));
    }

    /**
     * @param Picture              $picture
     * @param CheckPictureWorkflow $checkPictureWorkflow
     * @param InvalidatePicture    $invalidatePicture
     * @param Request              $request
     *
     * @return Response
     *
     * @Route("/{id}/invalidation", name="app_validation_pictures_invalidation", methods={"GET", "POST"})
     */
    public function invalidationAction(Picture $picture, CheckPictureWorkflow $checkPictureWorkflow, InvalidatePicture $invalidatePicture, Request $request): Response
    {
        if (!$checkPictureWorkflow->canApplyInvalidation($picture)) {
            throw new AccessDeniedException(sprintf('Check picture\'s workflow is not valid for execute this action to picture %s.', $picture->getId()));
        }

        $form = $this->formFactory->create(InvalidationPictureType::class, (new InvalidationPicture())->setPicture($picture));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invalidatePicture->execute($form->getData());
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'validation.pictures.invalidation.invalidation_ok');

            return new RedirectResponse($this->router->generate('app_validation_pictures_list'));
        }

        return new Response(
            $this->twig->render('validation/picture/invalidation.html.twig', [
                'form' => $form->createView(),
                'picture' => $picture,
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
            $this->twig->render('validation/picture/list.html.twig', [
                'form' => $form,
                'page' => $page,
                'nbPages' => $nbPages,
                'nbElements' => $nbElements,
                'elements' => $elements,
            ])
        );
    }
}
