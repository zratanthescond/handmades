<?php

namespace App\EventSubscriber;

use App\Event\OrderIsPlacedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderQuantitySubscriber implements EventSubscriberInterface
{
    
    private $em;

    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
        
    }

    public function onOrderIsPlaced(OrderIsPlacedEvent $event): void
    {
        $order = $event->getOrder();

        $productsOrder = $order->getProducts();

        foreach($productsOrder as $productOrder) {

             $product = $productOrder->getProduct();

             $orderQty = $productOrder->getQty();

             $currentQty = $product->getQty();

             $product->setQty($currentQty - $orderQty);

             $this->em->persist($product);
        }

        $this->em->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onOrderIsPlaced", 100]
        ];
    }
}
