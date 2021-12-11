<?php

namespace App\EventSubscriber;

use App\Core\RewardPoints\RewardPointsContext;
use App\Core\RewardPoints\RewardPointsManager;
use App\Entity\RewardPointsHistory;
use App\Event\OrderIsPlacedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EarnedRewardPointsFromOrderSubscriber implements EventSubscriberInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }

    public function onOrderIsPlaced(OrderIsPlacedEvent $event)
    {
        $order = $event->getOrder();

        $orderProducts = $order->getProducts()->toArray();

        $earnedPoints = 0;

        foreach ($orderProducts as $orderProduct) {

            $product = $orderProduct->getProduct();

            $rewardsPointsManger = new RewardPointsManager($product->getPrice());

            $points = $rewardsPointsManger->getPoints();

            $earnedPoints += $points;
        }

        if ($earnedPoints > 0) {

            $user = $order->getUser();

            $currentPoint = $user->getRewardPoints();

            $newPoints = $currentPoint + $earnedPoints;

            $history = (new RewardPointsHistory())
                ->setCurrentPoints($currentPoint)
                ->setPoints($earnedPoints)
                ->setNewPoints($newPoints)
                ->setContext(RewardPointsContext::ORDER)
                ->setOperation(RewardPointsContext::INCREMENT);

            $user->addRewardPointsHistory($history)->setRewardPoints($newPoints);

            $this->em->persist($user);

            $this->em->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onOrderIsPlaced", 10]
        ];
    }
}
