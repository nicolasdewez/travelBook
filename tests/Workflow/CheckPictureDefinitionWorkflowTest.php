<?php

namespace App\Tests\Workflow;

use App\Workflow\CheckPictureDefinitionWorkflow;
use PHPUnit\Framework\TestCase;

class CheckPictureDefinitionWorkflowTest extends TestCase
{
    public function testGetTitleByPlace()
    {
        $this->assertSame('', CheckPictureDefinitionWorkflow::getTitleByPlace(''));

        $this->assertSame(
            CheckPictureDefinitionWorkflow::PLACE_TITLE_UPLOADED,
            CheckPictureDefinitionWorkflow::getTitleByPlace(CheckPictureDefinitionWorkflow::PLACE_UPLOADED)
        );

        $this->assertSame(
            CheckPictureDefinitionWorkflow::PLACE_TITLE_HEALTHY,
            CheckPictureDefinitionWorkflow::getTitleByPlace(CheckPictureDefinitionWorkflow::PLACE_HEALTHY)
        );

        $this->assertSame(
            CheckPictureDefinitionWorkflow::PLACE_TITLE_VIRUS,
            CheckPictureDefinitionWorkflow::getTitleByPlace(CheckPictureDefinitionWorkflow::PLACE_VIRUS)
        );

        $this->assertSame(
            CheckPictureDefinitionWorkflow::PLACE_TITLE_VALIDATED,
            CheckPictureDefinitionWorkflow::getTitleByPlace(CheckPictureDefinitionWorkflow::PLACE_VALIDATED)
        );

        $this->assertSame(
            CheckPictureDefinitionWorkflow::PLACE_TITLE_INVALID,
            CheckPictureDefinitionWorkflow::getTitleByPlace(CheckPictureDefinitionWorkflow::PLACE_INVALID)
        );
    }
}
