<?php

namespace App\Core\Parrainage;

use App\Core\RewardPoints\RewardPointsManager;

class ParrainageManager extends RewardPointsManager
{

    /**
     * 4 D DE REDUCTION
     */

    public const value = 4;
        
    public function getPoints(): ?int
    {
        return ceil(self::value / self::BENEFITS_PER_POINT);
    }
}