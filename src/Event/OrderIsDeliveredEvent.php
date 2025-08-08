<?php

namespace App\Event;

use App\Entity\Order;

class OrderIsDeliveredEvent
{

    private Order $order;

    public const EVENT_NAME = "OrderIsDelivered";

    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    public function getOrder(): Order
    {
        return $this->order;
    }
}
