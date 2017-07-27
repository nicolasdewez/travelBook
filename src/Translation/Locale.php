<?php

namespace App\Translation;

final class Locale
{
    const FR = 'fr';
    const EN = 'en';

    /**
     * @return array
     */
    public static function getLocales(): array
    {
        return [self::FR, self::EN];
    }
}
