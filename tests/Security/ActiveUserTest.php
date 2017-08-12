<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\ActiveUser;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ActiveUserTest extends TestCase
{
    public function testExecute()
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $user = new User();

        $activeUser = new ActiveUser($manager, new NullLogger());
        $activeUser->execute($user);

        $this->assertFalse($user->isRegistrationInProgress());
        $this->assertTrue($user->isEnabled());
    }
}
