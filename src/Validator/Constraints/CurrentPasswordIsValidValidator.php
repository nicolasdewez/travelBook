<?php

namespace App\Validator\Constraints;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CurrentPasswordIsValidValidator extends ConstraintValidator
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($user, Constraint $constraint)
    {
        if (null === $user->getCurrentPassword() && !in_array($this->context->getGroup(), $constraint->groupsRequired)) {
            return;
        }

        if (!$this->encoder->isPasswordValid($user, $user->getCurrentPassword())) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
