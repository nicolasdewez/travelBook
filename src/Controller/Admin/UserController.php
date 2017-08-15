<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Type\FilterUserType;
use App\Manager\UserManager;
use App\Pagination\InformationPagination;
use App\Security\AskResendRegistration;
use App\Security\DisableAccount;
use App\Security\EnableAccount;
use App\Session\FilterStorage;
use App\Session\Flash;
use App\Workflow\RegistrationWorkflow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    /** @var SessionInterface */
    private $session;

    /** @var RouterInterface */
    private $router;

    /**
     * @param FormFactoryInterface $formFactory
     * @param Twig                 $twig
     * @param SessionInterface     $session
     * @param RouterInterface      $router
     */
    public function __construct(FormFactoryInterface $formFactory, Twig $twig, SessionInterface $session, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * @param int                   $page
     * @param Request               $request
     * @param UserManager           $manager
     * @param FilterStorage         $filterStorage
     * @param InformationPagination $pagination
     *
     * @return Response
     *
     * @Route("/list/{page}", name="app_admin_users_list", defaults={"page": 1}, methods={"GET", "POST"})
     */
    public function listAction(int $page, Request $request, UserManager $manager, FilterStorage $filterStorage, InformationPagination $pagination): Response
    {
        $filterUser = $filterStorage->getFilterUser();

        $form = $this->formFactory->create(FilterUserType::class, $filterUser);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $filterStorage->saveFilterUser($filterUser);
            if (!$form->isValid()) {
                return $this->getListResponse($form->createView(), 0, 0, 0, []);
            }
        }

        $nbElements = $manager->countElements($filterUser);
        $nbPages = $pagination->getNbPages($nbElements) ?: 1;
        if ($nbPages < $page) {
            return new RedirectResponse($this->router->generate('app_admin_users_list'));
        }

        return $this->getListResponse(
            $form->createView(),
            $page,
            $nbPages,
            $nbElements,
            $manager->listElements($filterUser, $page)
        );
    }

    /**
     * @param User                  $user
     * @param RegistrationWorkflow  $registrationWorkflow
     * @param AskResendRegistration $askResendRegistration
     *
     * @return Response
     *
     * @Route("/{id}/send-registration", name="app_admin_users_send_registration", methods={"GET"})
     */
    public function sendRegistrationAction(User $user, RegistrationWorkflow $registrationWorkflow, AskResendRegistration $askResendRegistration): Response
    {
        if (!$registrationWorkflow->canApplyRegistration($user)) {
            throw new AccessDeniedException(sprintf('Registration\'s workflow is not valid for execute this action to user %s.', $user->getUsername()));
        }

        $askResendRegistration->execute($user);

        $this->session->getFlashBag()->add(Flash::TYPE_NOTICE, 'admin.users.list.send_registration_ok');

        return new RedirectResponse($this->router->generate('app_admin_users_list'));
    }

    /**
     * @param User          $user
     * @param EnableAccount $enableAccount
     *
     * @return Response
     *
     * @Route("/{id}/enable", name="app_admin_users_enable", methods={"GET"})
     */
    public function enableAction(User $user, EnableAccount $enableAccount): Response
    {
        if ($user->isEnabled()) {
            throw new AccessDeniedException(sprintf('Enable account is not possible for user %s.', $user->getUsername()));
        }

        $enableAccount->execute($user);

        $this->session->getFlashBag()->add(Flash::TYPE_NOTICE, 'admin.users.list.enable_account_ok');

        return new RedirectResponse($this->router->generate('app_admin_users_list'));
    }

    /**
     * @param User           $user
     * @param DisableAccount $disableAccount
     *
     * @return Response
     *
     * @Route("/{id}/disable", name="app_admin_users_disable", methods={"GET"})
     */
    public function disableAction(User $user, DisableAccount $disableAccount): Response
    {
        if (!$user->isEnabled()) {
            throw new AccessDeniedException(sprintf('Disable account is not possible for user %s.', $user->getUsername()));
        }

        $disableAccount->execute($user);

        $this->session->getFlashBag()->add(Flash::TYPE_NOTICE, 'admin.users.list.disable_account_ok');

        return new RedirectResponse($this->router->generate('app_admin_users_list'));
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
