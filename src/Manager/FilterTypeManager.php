<?php

namespace App\Manager;

use App\Form\Type\FilterPictureType;
use App\Form\Type\FilterPlaceType;
use App\Form\Type\FilterUserType;
use App\Session\FilterStorage;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterTypeManager
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var FilterStorage */
    private $filterStorage;

    /**
     * @param FormFactoryInterface $formFactory
     * @param FilterStorage        $filterStorage
     */
    public function __construct(FormFactoryInterface $formFactory, FilterStorage $filterStorage)
    {
        $this->formFactory = $formFactory;
        $this->filterStorage = $filterStorage;
    }

    /**
     * @param Request $request
     *
     * @return FormInterface
     */
    public function executeToListUsers(Request $request): FormInterface
    {
        $filterUser = $this->filterStorage->getFilterUser();

        $form = $this->formFactory->create(FilterUserType::class, $filterUser);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->filterStorage->saveFilterUser($filterUser);
        }

        return $form;
    }

    /**
     * @param Request $request
     *
     * @return FormInterface
     */
    public function executeToListPlaces(Request $request): FormInterface
    {
        $filterPlace = $this->filterStorage->getFilterPlace();

        $form = $this->formFactory->create(FilterPlaceType::class, $filterPlace);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->filterStorage->saveFilterPlace($filterPlace);
        }

        return $form;
    }

    /**
     * @param Request $request
     *
     * @return FormInterface
     */
    public function executeToValidatePictures(Request $request): FormInterface
    {
        $filterPicture = $this->filterStorage->getFilterPicture();

        $form = $this->formFactory->create(FilterPictureType::class, $filterPicture);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->filterStorage->saveFilterPicture($filterPicture);
        }

        return $form;
    }

    /**
     * @param Request $request
     *
     * @return FormInterface
     */
    public function executeToReValidatePictures(Request $request): FormInterface
    {
        $filterPicture = $this->filterStorage->getFilterPictureProcessed();

        $form = $this->formFactory->create(
            FilterPictureType::class,
            $filterPicture,
            ['state_processed' => true, 'username' => true]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->filterStorage->saveFilterPictureProcessed($filterPicture);
        }

        return $form;
    }
}
