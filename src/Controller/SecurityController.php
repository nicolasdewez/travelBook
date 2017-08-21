<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\LoginType;
use App\Form\Type\MyAccountType;
use App\Form\Type\PasswordLostType;
use App\Form\Type\RegistrationType;
use App\Security\ActiveUser;
use App\Security\AskPasswordLost;
use App\Security\AskRegistration;
use App\Security\CheckRegistrationCode;
use App\Security\UpdateAccount;
use App\Security\ValidFirstConnection;
use App\Session\Flash;
use App\Session\FlashMessage;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment as Twig;

class SecurityController
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
     */
    public function __construct(FormFactoryInterface $formFactory, Twig $twig, RouterInterface $router, FlashMessage $flashMessage)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->flashMessage = $flashMessage;
    }

    /**
     * @param Request         $request
     * @param AskRegistration $askRegistration
     *
     * @return Response
     *
     * @Route("/registration", name="app_registration", methods={"GET", "POST"})
     */
    public function registrationAction(Request $request, AskRegistration $askRegistration): Response
    {
        $form = $this->formFactory->create(RegistrationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $askRegistration->execute($form->getData());

            $this->flashMessage->add(Flash::TYPE_NOTICE, 'registration.ok');

            return new RedirectResponse($this->router->generate('app_login'));
        }

        return new Response(
            $this->twig->render('security/registration.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param string                $registrationCode
     * @param CheckRegistrationCode $checker
     * @param ActiveUser            $activeUser
     *
     * @return Response
     *
     * @Route("/active/{registrationCode}", name="app_active", methods={"GET"})
     */
    public function activeAction(string $registrationCode, CheckRegistrationCode $checker, ActiveUser $activeUser): Response
    {
        if (!$checker->execute($registrationCode)) {
            throw new AccessDeniedHttpException('Access denied for user. Registration code or user are invalid.');
        }

        $activeUser->execute($checker->getUser());

        $this->flashMessage->add(Flash::TYPE_NOTICE, 'active.ok');

        return new RedirectResponse($this->router->generate('app_login'));
    }

    /**
     * @param Request         $request
     * @param AskPasswordLost $askPasswordLost
     *
     * @return Response
     *
     * @Route("/password-lost", name="app_password_lost", methods={"GET", "POST"})
     */
    public function passwordLostAction(Request $request, AskPasswordLost $askPasswordLost): Response
    {
        $form = $this->formFactory->create(PasswordLostType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $askPasswordLost->execute($form->getData());

            $this->flashMessage->add(Flash::TYPE_NOTICE, 'password_lost.ok');

            return new RedirectResponse($this->router->generate('app_login'));
        }

        return new Response(
            $this->twig->render('security/password-lost.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     *
     * @Route("/login", name="app_login", methods={"GET"})
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->formFactory->create(LoginType::class);

        return new Response(
            $this->twig->render('security/login.html.twig', [
                'form' => $form->createView(),
                'error' => $authenticationUtils->getLastAuthenticationError(),
            ])
        );
    }

    /**
     * @param Request              $request
     * @param UserInterface        $user
     * @param ValidFirstConnection $validFirstConnection
     *
     * @return Response
     *
     * @Route("/change-password", name="app_change_password", methods={"GET", "POST"})
     * @Security("user.isFirstConnection()")
     */
    public function changePasswordAction(Request $request, UserInterface $user, ValidFirstConnection $validFirstConnection): Response
    {
        $form = $this->formFactory->create(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $validFirstConnection->execute($user);
            $this->flashMessage->add(Flash::TYPE_NOTICE, 'change_password.ok');

            return new RedirectResponse($this->router->generate('app_travels'));
        }

        return new Response(
            $this->twig->render('security/change-password.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param Request       $request
     * @param UserInterface $user
     * @param UpdateAccount $updateAccount
     *
     * @return Response
     *
     * @Route("/my-account", name="app_my_account", methods={"GET", "POST"})
     */
    public function myAccountAction(Request $request, UserInterface $user, UpdateAccount $updateAccount): Response
    {
        $form = $this->formFactory->create(MyAccountType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updateAccount->execute($user);

            $this->flashMessage->add(Flash::TYPE_NOTICE, 'my_account.ok');

            return new RedirectResponse($this->router->generate('app_my_account'));
        }

        return new Response(
            $this->twig->render('security/my-account.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param LoggerInterface $logger
     * @param UserInterface   $user
     *
     * @return Response
     */
    public function userBarAction(LoggerInterface $logger, UserInterface $user = null): Response
    {
        if (null === $user) {
            return new Response();
        }

        if (!($user instanceof User)) {
            $logger->error(sprintf(
                'User %s is not a valid user (User instance expected, %s found)',
                $user->getUsername(),
                get_class($user)
            ));

            return new Response();
        }

        return new Response(
            $this->twig->render('common/user-bar.html.twig', [
                'username' => $user->getUsername(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
            ])
        );
    }
}
