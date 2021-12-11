<?php

namespace App\EventSubscriber;

use App\Core\Parrainage\ParrainageManager;
use App\Core\RewardPoints\RewardPointsContext;
use App\Entity\Parrainage;
use App\Entity\RewardPointsHistory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\OrderIsPlacedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ParrainageRewardSubscriber implements EventSubscriberInterface
{


    private $em;

    private $mailer;

    private $parrainageManager;

    public function __construct(EntityManagerInterface $em, MailerInterface $mailer, ParrainageManager $parrainageManager)
    {
        $this->em = $em;

        $this->parrainageManager = $parrainageManager;

        $this->mailer = $mailer;
    }

    public function onOrderIsPlaced(OrderIsPlacedEvent $event)
    {

        $order = $event->getOrder();

        $user = $order->getUser();

        $parrainageRepo = $this->em->getRepository(Parrainage::class);

        $parrainage = $parrainageRepo->findOneBy(
            [
                "beneficiaryEmail" => $user->getEmail(),
                "isRewarded" => false
            ]
        );

        if ($parrainage) {

            $fromUser = $parrainage->getFromUser();

            $currentPoint = $fromUser->getRewardPoints();

            $incrementedPoints = $this->parrainageManager->getPoints();

            $newPoints = $currentPoint + $incrementedPoints;

            $parrainage->setIsRewarded(true)->setIsRewardedAt(new \DateTimeImmutable());

            $history = (new RewardPointsHistory())
            ->setCurrentPoints($currentPoint)
            ->setPoints($incrementedPoints)
            ->setNewPoints($newPoints)
            ->setContext(RewardPointsContext::PARRAINAGE)
            ->setOperation(RewardPointsContext::INCREMENT);

            $fromUser->setRewardPoints($newPoints)->addRewardPointsHistory($history);
            
            $this->em->persist($fromUser);

            $this->em->persist($parrainage);

            $this->em->flush();

            $email = (new TemplatedEmail())->to($fromUser->getEmail())
                ->subject(sprintf("Vous venez de gagner %s points", $incrementedPoints))
                ->context([
                    "parrainage" => $parrainage,
                    "fromUser" => $fromUser,
                    "incrementedPoints" => $incrementedPoints
                ])
                ->htmlTemplate("email/parrainage/parrainage_is_rewarded.html.twig");

            $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onOrderIsPlaced", 20]
        ];
    }
}
