<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AramexPickUpDate extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
      

    public $message = 'Cette valeur "{{ value }}" n \'est pas valide. Veuillez saisir une heure entre 9h et 19h';
}
