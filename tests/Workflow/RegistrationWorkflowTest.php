<?php

namespace App\Tests\Workflow;

use App\Entity\User;
use App\Workflow\RegistrationDefinitionWorkflow as Definition;
use App\Workflow\RegistrationWorkflow;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\StateMachine;

class RegistrationWorkflowTest extends TestCase
{
    public function testCanApplyRegistration()
    {
        $user = new User();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($user, Definition::TRANSITION_REGISTRATION)
            ->willReturn(true)
        ;

        $registrationWorkflow = new RegistrationWorkflow($stateMachine);
        $this->assertTrue($registrationWorkflow->canApplyRegistration($user));
    }

    public function testCanApplyActive()
    {
        $user = new User();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($user, Definition::TRANSITION_ACTIVE)
            ->willReturn(true)
        ;

        $registrationWorkflow = new RegistrationWorkflow($stateMachine);
        $this->assertTrue($registrationWorkflow->canApplyActive($user));
    }

    public function testApplyRegistration()
    {
        $user = new User();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($user, Definition::TRANSITION_REGISTRATION)
        ;

        $registrationWorkflow = new RegistrationWorkflow($stateMachine);
        $registrationWorkflow->applyRegistration($user);
    }

    public function testApplyActive()
    {
        $user = new User();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($user, Definition::TRANSITION_ACTIVE)
        ;

        $registrationWorkflow = new RegistrationWorkflow($stateMachine);
        $registrationWorkflow->applyActive($user);
    }
}
