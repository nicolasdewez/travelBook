<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Validator\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UserTest extends TestCase
{
    public function testCurrentAndNewPasswordAreDifferentNoViolation()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('getGroup')->willReturn(Group::USER_MY_ACCOUNT);
        $context->expects($this->never())->method('buildViolation');

        $user = new User();
        $this->assertNull($user->currentAndNewPasswordAreDifferent($context, null));

        $user = (new User())
            ->setCurrentPassword('current')
            ->setNewPassword('new')
        ;

        $this->assertNull($user->currentAndNewPasswordAreDifferent($context, null));
    }

    public function testCurrentAndNewPasswordAreDifferentBuildViolation()
    {
        $constraint = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint->method('addViolation')->willReturn(true);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->method('getGroup')
            ->willReturn(Group::USER_CHANGE_PASSWORD)
        ;

        $context
            ->method('buildViolation')
            ->with('password.current_new_not_different')
            ->willReturn($constraint)
        ;

        $user = (new User())
            ->setCurrentPassword('password')
            ->setNewPassword('password')
        ;

        $this->assertNull($user->currentAndNewPasswordAreDifferent($context, null));
    }

    public function testCurrentAndNewPasswordAreEmptyOrNotNoViolation()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())->method('buildViolation');

        $user = new User();
        $this->assertNull($user->currentAndNewPasswordAreEmptyOrNot($context, null));

        $user = (new User())
            ->setCurrentPassword('current')
            ->setNewPassword('new')
        ;

        $this->assertNull($user->currentAndNewPasswordAreEmptyOrNot($context, null));
    }

    public function testCurrentANdNEwPasswordAreEmptyOrNotBuildViolation()
    {
        $constraint = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint->method('addViolation')->willReturn(true);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->method('getGroup')
            ->willReturn(Group::USER_CHANGE_PASSWORD)
        ;

        $context
            ->method('buildViolation')
            ->with('password.current_new_not_empty')
            ->willReturn($constraint)
        ;

        $user = (new User())->setCurrentPassword('password');

        $this->assertNull($user->currentAndNewPasswordAreDifferent($context, null));
    }
}
