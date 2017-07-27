<?php

namespace App\Handler;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    const DEFAULT_ROUTE = 'app_travels';
    const CHANGE_PASSWORD_ROUTE = 'app_change_password';

    /** @var RouterInterface */
    protected $router;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(RouterInterface $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $this->logger->error(sprintf(
                'User %s is not a valid user (User instance expected, %s found)',
                $user->getUsername(),
                get_class($user)
            ));

            return new RedirectResponse($this->router->generate(self::DEFAULT_ROUTE));
        }

        // if first connection redirect to page change password else redirect to home page
        if (!$user->isFirstConnection()) {
            return new RedirectResponse($this->router->generate(self::DEFAULT_ROUTE));
        }

        $this->logger->info(sprintf(
            'Redirect user %s to %s (first connection)',
            $user->getUsername(),
            self::CHANGE_PASSWORD_ROUTE
        ));

        return new RedirectResponse($this->router->generate(self::CHANGE_PASSWORD_ROUTE));
    }
}
