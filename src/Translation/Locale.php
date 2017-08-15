<?php

namespace App\Translation;

final class Locale
{
    const FR = 'fr';
    const EN = 'en';

    const TITLE_FR = 'locale.fr';
    const TITLE_EN = 'locale.en';

    /**
     * @return array
     */
    public static function getLocales(): array
    {
        return [self::FR, self::EN];
    }
}
