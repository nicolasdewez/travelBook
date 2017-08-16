<?php

namespace App\Tests\Security;

use App\Security\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testGetTitleByRole()
    {
        $this->assertSame('', Role::getTitleByRole(''));
        $this->assertSame(Role::TITLE_USER, Role::getTitleByRole(Role::USER));
        $this->assertSame(Role::TITLE_ADMIN, Role::getTitleByRole(Role::ADMIN));
        $this->assertSame(Role::TITLE_VALIDATOR, Role::getTitleByRole(Role::VALIDATOR));
    }
}
