<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\ActiveUser;
use App\Workflow\RegistrationWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ActiveUserTest extends TestCase
{
    public function testExecute()
    {
        $user = new User();

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('flush')
            ->withAnyParameters()
        ;

        $workflow = $this->createMock(RegistrationWorkflow::class);
        $workflow
            ->expects($this->once())
            ->method('applyActive')
            ->with($user)
        ;

        $activeUser = new ActiveUser($manager, $workflow, new NullLogger());
        $activeUser->execute($user);

        $this->assertTrue($user->isEnabled());
    }
}
