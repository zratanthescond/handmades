<?php

namespace App\Event;

use App\Entity\PromoterWithdrawalRequest;

class PromoterWithdrawalRequestEvent
{

    public const EVENT_NAME = 'promoter.withdrawal.is.requested';

    private PromoterWithdrawalRequest $withdrawalRequest;

    public function __construct(PromoterWithdrawalRequest $withdrawalRequest)
    {

        $this->withdrawalRequest = $withdrawalRequest;
    }


    public function getWithdrawalRequest(): PromoterWithdrawalRequest
    {
        return $this->withdrawalRequest;
    }
}
