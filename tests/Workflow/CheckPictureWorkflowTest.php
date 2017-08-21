<?php

namespace App\Tests\Workflow;

use App\Entity\Picture;
use App\Workflow\CheckPictureDefinitionWorkflow as Definition;
use App\Workflow\CheckPictureWorkflow;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\StateMachine;

class CheckPictureWorkflowTest extends TestCase
{
    public function testCanApplyAnalyze()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->exactly(2))
            ->method('can')
            ->withConsecutive([$picture, Definition::TRANSITION_ANALYZE_OK], [$picture, Definition::TRANSITION_ANALYZE_KO])
            ->willReturn(true)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $this->assertTrue($checkPictureWorkflow->canApplyAnalyze($picture));
    }

    public function testCanApplyAnalyzeOk()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($picture, Definition::TRANSITION_ANALYZE_OK)
            ->willReturn(true)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $this->assertTrue($checkPictureWorkflow->canApplyAnalyzeOk($picture));
    }

    public function testCanApplyAnalyzeKo()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($picture, Definition::TRANSITION_ANALYZE_KO)
            ->willReturn(true)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $this->assertTrue($checkPictureWorkflow->canApplyAnalyzeKo($picture));
    }

    public function testCanApplyValidation()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($picture, Definition::TRANSITION_VALIDATION)
            ->willReturn(true)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $this->assertTrue($checkPictureWorkflow->canApplyValidation($picture));
    }

    public function testCanApplyInvalidation()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($picture, Definition::TRANSITION_INVALIDATION)
            ->willReturn(true)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $this->assertTrue($checkPictureWorkflow->canApplyInvalidation($picture));
    }

    public function testApplyAnalyzeOk()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($picture, Definition::TRANSITION_ANALYZE_OK)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $checkPictureWorkflow->applyAnalyzeOk($picture);
    }
    
    public function testApplyAnalyzeKo()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($picture, Definition::TRANSITION_ANALYZE_KO)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $checkPictureWorkflow->applyAnalyzeKo($picture);
    }
    
    public function testApplyValidation()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($picture, Definition::TRANSITION_VALIDATION)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $checkPictureWorkflow->applyValidation($picture);
    }

    public function testApplyInvalidation()
    {
        $picture = new Picture();

        $stateMachine = $this->createMock(StateMachine::class);
        $stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($picture, Definition::TRANSITION_INVALIDATION)
        ;

        $checkPictureWorkflow = new CheckPictureWorkflow($stateMachine);
        $checkPictureWorkflow->applyInvalidation($picture);
    }
}
