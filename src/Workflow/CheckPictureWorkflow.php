<?php

namespace App\Workflow;

use App\Entity\Picture;
use App\Workflow\CheckPictureDefinitionWorkflow as Definition;
use Symfony\Component\Workflow\StateMachine;

class CheckPictureWorkflow
{
    /** @var StateMachine */
    private $stateMachine;

    /**
     * @param StateMachine $stateMachine
     */
    public function __construct(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * @param Picture $picture
     *
     * @return bool
     */
    public function canApplyAnalyze(Picture $picture): bool
    {
        return $this->canApplyAnalyzeOk($picture) && $this->canApplyAnalyzeKo($picture);
    }

    /**
     * @param Picture $picture
     *
     * @return bool
     */
    public function canApplyAnalyzeOk(Picture $picture): bool
    {
        return $this->stateMachine->can($picture, Definition::TRANSITION_ANALYZE_OK);
    }

    /**
     * @param Picture $picture
     *
     * @return bool
     */
    public function canApplyAnalyzeKo(Picture $picture): bool
    {
        return $this->stateMachine->can($picture, Definition::TRANSITION_ANALYZE_KO);
    }

    /**
     * @param Picture $picture
     *
     * @return bool
     */
    public function canApplyValidation(Picture $picture): bool
    {
        return $this->stateMachine->can($picture, Definition::TRANSITION_VALIDATION);
    }

    /**
     * @param Picture $picture
     *
     * @return bool
     */
    public function canApplyInvalidation(Picture $picture): bool
    {
        return $this->stateMachine->can($picture, Definition::TRANSITION_INVALIDATION);
    }

    /**
     * @param Picture $picture
     */
    public function applyAnalyzeOk(Picture $picture)
    {
        $this->stateMachine->apply($picture, Definition::TRANSITION_ANALYZE_OK);
    }

    /**
     * @param Picture $picture
     */
    public function applyAnalyzeKo(Picture $picture)
    {
        $this->stateMachine->apply($picture, Definition::TRANSITION_ANALYZE_KO);
    }

    /**
     * @param Picture $picture
     */
    public function applyValidation(Picture $picture)
    {
        $this->stateMachine->apply($picture, Definition::TRANSITION_VALIDATION);
    }

    /**
     * @param Picture $picture
     */
    public function applyInvalidation(Picture $picture)
    {
        $this->stateMachine->apply($picture, Definition::TRANSITION_INVALIDATION);
    }
}
