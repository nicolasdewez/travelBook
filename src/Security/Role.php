<?php

namespace App\Security;

final class Role
{
    const USER = 'ROLE_USER';
    const ADMIN = 'ROLE_ADMIN';
    const VALIDATOR = 'ROLE_VALIDATOR';
    const CALLER = 'ROLE_CALLER';

    const TITLE_USER = 'role.user';
    const TITLE_ADMIN = 'role.admin';
    const TITLE_VALIDATOR = 'role.validator';
    const TITLE_CALLER = 'role.caller';

    const TITLES = [
        self::USER => self::TITLE_USER,
        self::ADMIN => self::TITLE_ADMIN,
        self::VALIDATOR => self::TITLE_VALIDATOR,
        self::CALLER => self::TITLE_CALLER,
    ];

    /**
     * @return array
     */
    public static function getRoles(): array
    {
        return [self::USER, self::ADMIN, self::VALIDATOR, self::CALLER];
    }

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
