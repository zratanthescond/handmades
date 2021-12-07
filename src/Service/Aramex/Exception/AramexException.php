<?php

namespace App\Service\Aramex\Exception;

class AramexException extends \LogicException
{

    public function getError(): string
    {

        return "Une erreur est survenue";
    }
}
