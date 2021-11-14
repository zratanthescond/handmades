<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\OrderIsPlacedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ProductOutStockSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onOrderIsPlaced(OrderIsPlacedEvent $event)
    {
        $outOfStock = [];

        $order = $event->getOrder();

        $productsOrder = $order->getProducts();

        foreach ($productsOrder as $productOrder) {

            $product = $productOrder->getProduct();

            $qty = $product->getQty();

            if ($qty <= 0) {

                array_push($outOfStock, $product);
            }
        }

        /// send email to notify admin

        if (!empty($outOfStock)) {

            $subject = count($outOfStock) . " produit(s) en rupture de stock";

            $email = (new TemplatedEmail())
                ->to("mrbileltn@gmail.com")
                ->subject($subject)
                ->htmlTemplate('email/admin/notification/out_of_stock.html.twig')
                ->context([
                    'outOfStock' => $outOfStock,
                ]);

                $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onOrderIsPlaced", 50]
        ];
    }
}
