<?php

namespace App\Workflow;

final class RegistrationDefinitionWorkflow
{
    const PLACE_CREATED = 'created';
    const PLACE_REGISTERED = 'registered';
    const PLACE_ACTIVATED = 'activated';

    const TRANSITION_REGISTRATION = 'registration';
    const TRANSITION_ACTIVE = 'active';

    const PLACE_TITLE_CREATED = 'workflow.registration.created';
    const PLACE_TITLE_REGISTERED = 'workflow.registration.registered';
    const PLACE_TITLE_ACTIVATED = 'workflow.registration.activated';

    const PLACES_TITLES = [
        self::PLACE_CREATED => self::PLACE_TITLE_CREATED,
        self::PLACE_REGISTERED => self::PLACE_TITLE_REGISTERED,
        self::PLACE_ACTIVATED => self::PLACE_TITLE_ACTIVATED,
    ];

    /**
     * @param string $place
     *
     * @return string
     */
    public static function getTitleByPlace(string $place): string
    {
        if (!isset(self::PLACES_TITLES[$place])) {
            return '';
        }

        return self::PLACES_TITLES[$place];
    }
}
