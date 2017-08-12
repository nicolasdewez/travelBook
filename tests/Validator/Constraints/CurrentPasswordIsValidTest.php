<?php

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\CurrentPasswordIsValid;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

class CurrentPasswordIsValidTest extends TestCase
{
    public function testGetTargets()
    {
        $currentPasswordIsValid = new CurrentPasswordIsValid();

        $this->assertSame(Constraint::CLASS_CONSTRAINT, $currentPasswordIsValid->getTargets());
    }

    public function testClass()
    {
        $currentPasswordIsValid = new CurrentPasswordIsValid();

        $this->assertSame([], $currentPasswordIsValid->groupsRequired);
        $this->assertNotSame('', $currentPasswordIsValid->message);
    }
}
