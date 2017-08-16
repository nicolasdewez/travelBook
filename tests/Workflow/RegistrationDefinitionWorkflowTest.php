<?php

namespace App\Tests\Workflow;

use App\Workflow\RegistrationDefinitionWorkflow;
use PHPUnit\Framework\TestCase;

class RegistrationDefinitionWorkflowTest extends TestCase
{
    public function testGetTitleByPlace()
    {
        $this->assertSame('', RegistrationDefinitionWorkflow::getTitleByPlace(''));

        $this->assertSame(
            RegistrationDefinitionWorkflow::PLACE_TITLE_CREATED,
            RegistrationDefinitionWorkflow::getTitleByPlace(RegistrationDefinitionWorkflow::PLACE_CREATED)
        );

        $this->assertSame(
            RegistrationDefinitionWorkflow::PLACE_TITLE_REGISTERED,
            RegistrationDefinitionWorkflow::getTitleByPlace(RegistrationDefinitionWorkflow::PLACE_REGISTERED)
        );

        $this->assertSame(
            RegistrationDefinitionWorkflow::PLACE_TITLE_ACTIVATED,
            RegistrationDefinitionWorkflow::getTitleByPlace(RegistrationDefinitionWorkflow::PLACE_ACTIVATED)
        );
    }
}
