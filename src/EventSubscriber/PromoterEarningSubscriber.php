<?php

namespace App\EventSubscriber;

use App\Core\Promoter\Eearning\Earning;
use App\Entity\PromoterEarning;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\OrderIsPlacedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;

class PromoterEarningSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $em;

    private $mailer;

    public function __construct(EntityManagerInterface $em, MailerInterface $mailer)
    {

        $this->em = $em;

        $this->mailer = $mailer;
    }

    public function onOrderIsPlaced(OrderIsPlacedEvent $event)
    {
        $order = $event->getOrder();

        $discountCode = $order->getDiscountCode();

        if ($discountCode) {

            $promoter = $discountCode->getPromoter();

            if ($promoter) {

                $createdAt = $order->getCreatedAt();

                $earning = new PromoterEarning();

                $percent = Earning::EARNING_PERCENT_PER_ORDER;

                $earning->setCreatedAt($createdAt)
                    ->setPercent($percent)
                    ->setCOrder($order)
                    ->setPromoter($promoter)
                    ->setDiscountCode($discountCode);

                $currentBlance = $promoter->getBalance();

                $orderTotal = $order->getTotal();

                $income = ($orderTotal * $percent) / 100;

                $earning->setIncome($income);

                $newBalance = $currentBlance + $income;

                $promoter->setBalance($newBalance);

                $promoter->addEarning($earning);
                
                $this->em->persist($promoter);

                $this->em->flush();
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onOrderIsPlaced", 60]
        ];
    }
}
