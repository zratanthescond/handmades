<?php

namespace App\EventSubscriber;

use App\Core\RewardPoints\RewardPointsContext;
use App\Entity\RewardPointsHistory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\OrderIsPlacedEvent;
use Doctrine\ORM\EntityManagerInterface;

class RewardPointsToConsumeSubscriber implements EventSubscriberInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onOrderIsPlaced(OrderIsPlacedEvent $event)
    {
        $order = $event->getOrder();

        $rewardPointsToConsume = $order->getRewardPointsToConsume();

        if ($rewardPointsToConsume && $rewardPointsToConsume > 0) {
            
            $user = $order->getUser();

            $currentPoints = $user->getRewardPoints();

            $newPoints = $currentPoints - $rewardPointsToConsume;

            $user->setRewardPoints($newPoints);

            $history = (new RewardPointsHistory())
                ->setUser($user)
                ->setCurrentPoints($currentPoints)
                ->setNewPoints($newPoints)
                ->setPoints($rewardPointsToConsume)
                ->setContext(RewardPointsContext::ORDER)
                ->setOperation(RewardPointsContext::DECREMENT);

            $user->addRewardPointsHistory($history);
            
            $this->em->persist($user);

            $this->em->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onOrderIsPlaced", 40]
        ];
    }
}
