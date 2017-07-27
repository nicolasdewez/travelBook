<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshToken
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param UserInterface $user
     */
    public function execute(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, 'users', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }
}
