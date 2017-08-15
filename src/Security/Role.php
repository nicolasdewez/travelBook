<?php

namespace App\Security;

final class Role
{
    const USER = 'ROLE_USER';
    const ADMIN = 'ROLE_ADMIN';
    const VALIDATOR = 'ROLE_VALIDATOR';

    const TITLE_USER = 'role.user';
    const TITLE_ADMIN = 'role.admin';
    const TITLE_VALIDATOR = 'role.validator';

    const TITLES = [
        self::USER => self::TITLE_USER,
        self::ADMIN => self::TITLE_ADMIN,
        self::VALIDATOR => self::TITLE_VALIDATOR,
    ];

    /**
     * @param string $role
     *
     * @return string
     */
    public static function getTitleByRole(string $role): string
    {
        if (!isset(self::TITLES[$role])) {
            return '';
        }

        return self::TITLES[$role];
    }
}
