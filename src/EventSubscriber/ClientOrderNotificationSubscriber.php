<?php

namespace App\EventSubscriber;

use App\Core\RewardPoints\RewardPointsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\OrderIsPlacedEvent;
use App\Service\Mailer\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ClientOrderNotificationSubscriber implements EventSubscriberInterface
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onOrderIsPlaced(OrderIsPlacedEvent $event)
    {

        $order = $event->getOrder();

        $client = $order->getUser();

        $freeDelivery = $order->getTotal() >= 99;

        $discount = 0;

        $discount = +$order->getTotal() - $order->getSubtotal();

        if ($freeDelivery) {

            $discount += 7;
        } else {

            $discount -= 7;
        }

        $pm = new RewardPointsManager($order->getTotal());

        try {

            $email = (new TemplatedEmail())
                ->to($client->getEmail())
                ->cc(Mailer::ORDER_EMAIL)
                ->subject("Nous avons bien reçu votre commande")
                ->htmlTemplate('email/client/new_order.html.twig')
                ->context([
                    "client" => $client,
                    "order" => $order,
                    "discount" => $discount,
                    "pointsDiscount" => $pm->getValue(),
                    "points" => $pm->getPoints()
                ]);

            $this->mailer->send($email);
        } catch (\Exception $e) {
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onOrderIsPlaced", 5]
        ];
    }
}
