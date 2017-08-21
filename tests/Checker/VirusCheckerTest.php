<?php

namespace App\Tests\Checker;

use App\Checker\VirusChecker;
use PHPUnit\Framework\TestCase;

class VirusCheckerTest extends TestCase
{
    public function testExecute()
    {
        $this->assertTrue((new VirusChecker())->execute(new \SplFileInfo('')));
    }
}
