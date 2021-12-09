<?php

namespace App\EventSubscriber;

use App\Event\NewParrainageEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

class NewParrainageSubscriber implements EventSubscriberInterface
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onNewParrainageIsPlaced(NewParrainageEvent $event)
    {
        $parrainage = $event->getParrainage();

        $fromUser = $parrainage->getFromUser();

        $email = (new TemplatedEmail())->to($parrainage->getBeneficiaryEmail())
            ->subject(sprintf("%s souhaite vous parrainer", $fromUser->getFirstName()))
            ->htmlTemplate("email/parrainage/new_parrainage.html.twig")
            ->context([
                "parrainage" => $parrainage,
                "fromUser" => $fromUser
            ]);

        $this->mailer->send($email);    
    }

    public static function getSubscribedEvents()
    {
        return [
            NewParrainageEvent::EVENT_NAME => 'onNewParrainageIsPlaced',
        ];
    }
}
