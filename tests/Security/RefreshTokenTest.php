<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\RefreshToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RefreshTokenTest extends TestCase
{
    public function testExecute()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('setToken')
        ;

        $refreshToken = new RefreshToken($tokenStorage);
        $refreshToken->execute(new User());
    }
}
