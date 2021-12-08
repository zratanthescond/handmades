<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AramexPickUpDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\AramexPickUpDate */

        if (null === $value || '' === $value) {
            return;
        }

        if ($value instanceof \DateTime) {

            $strValue = $value->format("d/m/Y H:i:s");

            $min = 9;

            $max = 19;

            $hour = (int) $value->format("H");

            // TODO: implement the validation here
              
            if($hour < $min || $hour > $max) {

                $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $strValue)
                ->addViolation();
            }
        }
    }
}
