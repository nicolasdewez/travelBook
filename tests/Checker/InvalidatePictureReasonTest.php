<?php

namespace App\Tests\Security;

use App\Checker\InvalidatePictureReason;
use PHPUnit\Framework\TestCase;

class InvalidatePictureReasonTest extends TestCase
{
    public function testGetTitleByRole()
    {
        $this->assertSame('', InvalidatePictureReason::getTitle(''));
        $this->assertSame(InvalidatePictureReason::TITLE_INVALID, InvalidatePictureReason::getTitle(InvalidatePictureReason::INVALID));
        $this->assertSame(InvalidatePictureReason::TITLE_PORN, InvalidatePictureReason::getTitle(InvalidatePictureReason::PORN));
        $this->assertSame(InvalidatePictureReason::TITLE_PEDOPHILIA, InvalidatePictureReason::getTitle(InvalidatePictureReason::PEDOPHILIA));
        $this->assertSame(InvalidatePictureReason::TITLE_OTHERS, InvalidatePictureReason::getTitle(InvalidatePictureReason::OTHERS));
    }
}
