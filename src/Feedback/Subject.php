<?php

namespace App\Feedback;

final class Subject
{
    const INVALID_PICTURE = 'INVALID_PICTURE';
    const OTHER = 'OTHER';

    const TITLE_INVALID_PICTURE = 'feedback_subject.invalid_picture';
    const TITLE_OTHER = 'feedback_subject.other';

    const TITLES = [
        self::INVALID_PICTURE => self::TITLE_INVALID_PICTURE,
        self::OTHER => self::TITLE_OTHER,
    ];

    /**
     * @return array
     */
    public static function getSubjects(): array
    {
        return [self::INVALID_PICTURE, self::OTHER];
    }

    /**
     * @param string $subject
     *
     * @return string
     */
    public static function getTitleBySubject(string $subject): string
    {
        if (!isset(self::TITLES[$subject])) {
            return '';
        }

        return self::TITLES[$subject];
    }
}
