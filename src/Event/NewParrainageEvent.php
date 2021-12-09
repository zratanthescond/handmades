<?php

namespace App\Event;

use App\Entity\Parrainage;

class NewParrainageEvent
{

    private Parrainage $parrainage;

    public const EVENT_NAME = "new.parrainage.is.placed";

    public function __construct(Parrainage $parrainage)
    {
        $this->parrainage = $parrainage;
    }

   
    public function getParrainage(): Parrainage
    {
        return $this->parrainage;
    }
}