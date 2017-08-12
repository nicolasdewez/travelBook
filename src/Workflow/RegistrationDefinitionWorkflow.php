<?php

namespace App\Workflow;

final class RegistrationDefinitionWorkflow
{
    const PLACE_CREATED = 'created';
    const PLACE_REGISTERED = 'registered';
    const PLACE_ACTIVE = 'active';

    const TRANSITION_REGISTRATION = 'registration';
    const TRANSITION_ACTIVE = 'active';
}
