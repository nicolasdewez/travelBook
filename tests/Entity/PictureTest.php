<?php

namespace App\Tests\Entity;

use App\Entity\Picture;
use App\Workflow\CheckPictureDefinitionWorkflow;
use PHPUnit\Framework\TestCase;

class PictureTest extends TestCase
{
    public function testIsValidated()
    {
        $picture = new Picture();
        $picture->setCheckState('');

        $this->assertFalse($picture->isValidated());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_VALIDATED);
        $this->assertTrue($picture->isValidated());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_HEALTHY);
        $this->assertFalse($picture->isValidated());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_UPLOADED);
        $this->assertFalse($picture->isValidated());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_INVALID);
        $this->assertFalse($picture->isValidated());
    }

    public function testIsValidationInProgress()
    {
        $picture = new Picture();
        $picture->setCheckState('');

        $this->assertFalse($picture->isValidationInProgress());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_UPLOADED);
        $this->assertTrue($picture->isValidationInProgress());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_HEALTHY);
        $this->assertTrue($picture->isValidationInProgress());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_INVALID);
        $this->assertFalse($picture->isValidationInProgress());

        $picture->setCheckState(CheckPictureDefinitionWorkflow::PLACE_VALIDATED);
        $this->assertFalse($picture->isValidationInProgress());
    }
}
