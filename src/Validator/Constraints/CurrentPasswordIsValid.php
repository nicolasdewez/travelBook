<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrentPasswordIsValid extends Constraint
{
    /** @var string */
    public $message = 'password.current_not_valid';

    /** @var array */
    public $groupsRequired = [];

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
