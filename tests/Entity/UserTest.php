<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Security\Role;
use App\Validator\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UserTest extends TestCase
{
    public function testCurrentAndNewPasswordAreDifferentNoViolation()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->once())
            ->method('getGroup')
            ->withAnyParameters()
            ->willReturn(Group::USER_MY_ACCOUNT)
        ;

        $context
            ->expects($this->never())
            ->method('buildViolation')
        ;

        $user = new User();
        $this->assertNull($user->currentAndNewPasswordAreDifferent($context, null));

        $user = (new User())
            ->setCurrentPassword('current')
            ->setNewPassword('new')
        ;

        $user->currentAndNewPasswordAreDifferent($context, null);
    }

    public function testCurrentAndNewPasswordAreDifferentBuildViolation()
    {
        $constraint = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint
            ->expects($this->once())
            ->method('addViolation')
            ->withAnyParameters()
            ->willReturn(true)
        ;

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->never())
            ->method('getGroup')
        ;

        $context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('password.current_new_not_different')
            ->willReturn($constraint)
        ;

        $user = (new User())
            ->setCurrentPassword('password')
            ->setNewPassword('password')
        ;

        $user->currentAndNewPasswordAreDifferent($context, null);
    }

    public function testGetTitleRoles()
    {
        $user = (new User())->setRoles([Role::USER, Role::ADMIN, Role::VALIDATOR]);
        $this->assertSame([Role::TITLE_USER, Role::TITLE_ADMIN, Role::TITLE_VALIDATOR], $user->getTitleRoles());
    }
}
