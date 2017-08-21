<?php

namespace App\Checker;

final class InvalidatePictureReason
{
    const INVALID = 'invalid';
    const PORN = 'porn';
    const PEDOPHILIA = 'pedophilia';
    const OTHERS = 'others';

    const TITLE_INVALID = 'invalidation_picture_reason.invalid';
    const TITLE_PORN = 'invalidation_picture_reason.porn';
    const TITLE_PEDOPHILIA = 'invalidation_picture_reason.pedophilia';
    const TITLE_OTHERS = 'invalidation_picture_reason.others';

    const TITLES = [
        self::INVALID => self::TITLE_INVALID,
        self::PORN => self::TITLE_PORN,
        self::PEDOPHILIA => self::TITLE_PEDOPHILIA,
        self::OTHERS => self::TITLE_OTHERS,
    ];
}
