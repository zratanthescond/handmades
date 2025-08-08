<?php

namespace App\EventSubscriber;

use App\Event\OrderIsDeliveredEvent;
use App\Service\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class OrderIsDeliveredSubscriber implements EventSubscriberInterface
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {

        $this->mailer = $mailer;
    }

    public function onOrderIsDelivered(OrderIsDeliveredEvent $event)
    {
        $order = $event->getOrder();

        $client = $order->getUser();

        try {

            $email = (new TemplatedEmail())
                ->to($client->getEmail())
                ->cc(Mailer::ORDER_EMAIL)
                ->subject("Votre commande a été livrée")
                ->htmlTemplate('email/client/order_review.html.twig')
                ->context([
                    "order" => $order,
                    "client" => $client
                ]);

            $this->mailer->send($email);
        } catch (\Exception $e) {
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsDeliveredEvent::EVENT_NAME => 'onOrderIsDelivered',
        ];
    }
}
