<?php

namespace App\Event;

use App\Entity\Order;

class OrderIsPlacedEvent
{

    const EVENT_NAME = "order.is.placed";

    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

   
    public function getOrder(): Order
    {
        return $this->order;
    }
}