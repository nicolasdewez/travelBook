<?php

namespace App\Workflow;

final class CheckPictureDefinitionWorkflow
{
    const PLACE_UPLOADED = 'uploaded';
    const PLACE_HEALTHY = 'healthy';
    const PLACE_VIRUS = 'virus';
    const PLACE_VALIDATED = 'validated';
    const PLACE_INVALID = 'invalid';

    const TRANSITION_ANALYZE_OK = 'analyze_ok';
    const TRANSITION_ANALYZE_KO = 'analyze_ko';
    const TRANSITION_VALIDATION = 'validation';
    const TRANSITION_INVALIDATION = 'invalidation';

    const PLACE_TITLE_UPLOADED = 'workflow.check_picture.uploaded';
    const PLACE_TITLE_HEALTHY = 'workflow.check_picture.healthy';
    const PLACE_TITLE_VIRUS = 'workflow.check_picture.virus';
    const PLACE_TITLE_VALIDATED = 'workflow.check_picture.validated';
    const PLACE_TITLE_INVALID = 'workflow.check_picture.invalid';

    const PLACES_TITLES = [
        self::PLACE_UPLOADED => self::PLACE_TITLE_UPLOADED,
        self::PLACE_HEALTHY => self::PLACE_TITLE_HEALTHY,
        self::PLACE_VIRUS => self::PLACE_TITLE_VIRUS,
        self::PLACE_VALIDATED => self::PLACE_TITLE_VALIDATED,
        self::PLACE_INVALID => self::PLACE_TITLE_INVALID,
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
