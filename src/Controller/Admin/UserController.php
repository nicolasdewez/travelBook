<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Type\EditUserType;
use App\Form\Type\RegistrationType;
use App\Manager\FilterTypeManager;
use App\Manager\UserManager;
use App\Pagination\InformationPagination;
use App\Security\AskRegistration;
use App\Security\AskResendRegistration;
use App\Security\DisableAccount;
use App\Security\EnableAccount;
use App\Session\Flash;
use App\Session\FlashMessage;
use App\Workflow\RegistrationWorkflow;
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
 * @Route("/admin/users")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController
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
     * @param UserManager           $manager
     * @param FilterTypeManager     $filterManager
     * @param InformationPagination $pagination
     *
     * @return Response
     *
     * @Route(
     *     "/list/{page}",
     *     name="app_admin_users_list",
     *     requirements={"page": "^\d+$"},
     *     defaults={"page": 1},
     *     methods={"GET", "POST"}
     * )
     */
    public function listAction(int $page, Request $request, UserManager $manager, FilterTypeManager $filterManager, InformationPagination $pagination): Response
    {
        $form = $filterManager->executeToListUsers($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->getListResponse($form->createView(), 0, 0, 0, []);
        }

        $nbElements = $manager->countElements($form->getData());
        $nbPages = $pagination->getNbPages($nbElements) ?: 1;
        if ($nbPages < $page) {
            return new RedirectResponse($this->router->generate('app_admin_users_list'));
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
     * @param User                  $user
     * @param RegistrationWorkflow  $registrationWorkflow
     * @param AskResendRegistration $askResendRegistration
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/send-registration",
     *     name="app_admin_users_send_registration",
     *     requirements={"id": "^\d+$"},
     *     methods={"GET"}
     * )
     */
    public function sendRegistrationAction(User $user, RegistrationWorkflow $registrationWorkflow, AskResendRegistration $askResendRegistration): Response
    {
        if (!$registrationWorkflow->canApplyRegistration($user)) {
            throw new AccessDeniedException(sprintf('Registration\'s workflow is not valid for execute this action to user %s.', $user->getUsername()));
        }

        $askResendRegistration->execute($user);

        $this->flashMessage->add(Flash::TYPE_NOTICE, 'admin.users.list.send_registration_ok');

        return new RedirectResponse($this->router->generate('app_admin_users_list'));
    }

    /**
     * @param User          $user
     * @param EnableAccount $enableAccount
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/enable",
     *     name="app_admin_users_enable",
     *     requirements={"id": "^\d+$"},
     *     methods={"GET"}
     * )
     */
    public function enableAction(User $user, EnableAccount $enableAccount): Response
    {
        if ($user->isEnabled()) {
            throw new AccessDeniedException(sprintf('Enable account is not possible for user %s.', $user->getUsername()));
        }

        $enableAccount->execute($user);

        $this->flashMessage->add(Flash::TYPE_NOTICE, 'admin.users.list.enable_account_ok');

        return new RedirectResponse($this->router->generate('app_admin_users_list'));
    }

    /**
     * @param User           $user
     * @param DisableAccount $disableAccount
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/disable",
     *     name="app_admin_users_disable",
     *     requirements={"id": "^\d+$"},
     *     methods={"GET"}
     * )
     */
    public function disableAction(User $user, DisableAccount $disableAccount): Response
    {
        if (!$user->isEnabled()) {
            throw new AccessDeniedException(sprintf('Disable account is not possible for user %s.', $user->getUsername()));
        }

        $disableAccount->execute($user);

        $this->flashMessage->add(Flash::TYPE_NOTICE, 'admin.users.list.disable_account_ok');

        return new RedirectResponse($this->router->generate('app_admin_users_list'));
    }

    /**
     * @param User        $user
     * @param Request     $request
     * @param UserManager $userManager
     *
     * @return Response
     *
     * @Route(
     *     "/{id}/edit",
     *     name="app_admin_users_edit",
     *     requirements={"id": "^\d+$"},
     *     methods={"GET", "POST"}
     * )
     */
    public function editAction(User $user, Request $request, UserManager $userManager): Response
    {
        $form = $this->formFactory->create(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->save($user);
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'admin.users.edit.ok');

            return new RedirectResponse($this->router->generate('app_admin_users_list'));
        }

        return new Response(
            $this->twig->render('admin/user/edit.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param Request         $request
     * @param AskRegistration $askRegistration
     *
     * @return Response
     *
     * @Route("/create", name="app_admin_users_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, AskRegistration $askRegistration): Response
    {
        $form = $this->formFactory->create(RegistrationType::class, new User(), ['with_roles' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $askRegistration->execute($form->getData());
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'admin.users.create.ok');

            return new RedirectResponse($this->router->generate('app_admin_users_list'));
        }

        return new Response(
            $this->twig->render('admin/user/create.html.twig', [
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
            $this->twig->render('admin/user/list.html.twig', [
                'form' => $form,
                'page' => $page,
                'nbPages' => $nbPages,
                'nbElements' => $nbElements,
                'elements' => $elements,
            ])
        );
    }
}
