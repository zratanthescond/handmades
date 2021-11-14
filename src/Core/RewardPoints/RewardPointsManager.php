<?php

namespace App\Core\RewardPoints;

class RewardPointsManager
{

    /**
     * Logic
     * 1 point pour chaque 10 dt d'achat
     * 1 points = 0.300 TND
     */

    use RewardPointsTrait;

    const AMOUNT_MEASURE = 10;

    const BENEFITS_PER_POINT = 0.300;

    private $price;

    public function __construct(float $price)
    {
        $this->price = $price;
    }

    public function getPoints(): ?int
    {
        $points = $this->price / Self::AMOUNT_MEASURE;

        return (int) ceil($points);
    }

    public function getValue(): ?float
    {

        $c = $this->getPoints() * Self::BENEFITS_PER_POINT;
           
        return \number_format($c, 1);

    }

   
}
