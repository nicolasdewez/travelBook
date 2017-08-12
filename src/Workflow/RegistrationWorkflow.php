<?php

namespace App\Workflow;

use App\Entity\User;
use App\Workflow\RegistrationDefinitionWorkflow as Definition;
use Symfony\Component\Workflow\StateMachine;

class RegistrationWorkflow
{
    /** @var StateMachine */
    private $stateMachine;

    /**
     * @param StateMachine $stateMachine
     */
    public function __construct(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function canApplyRegistration(User $user): bool
    {
        return $this->stateMachine->can($user, Definition::TRANSITION_REGISTRATION);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function canApplyActive(User $user): bool
    {
        return $this->stateMachine->can($user, Definition::TRANSITION_ACTIVE);
    }

    /**
     * @param User $user
     */
    public function applyRegistration(User $user)
    {
        $this->stateMachine->apply($user, Definition::TRANSITION_REGISTRATION);
    }

    /**
     * @param User $user
     */
    public function applyActive(User $user)
    {
        $this->stateMachine->apply($user, Definition::TRANSITION_ACTIVE);
    }
}
