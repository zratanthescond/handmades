<?php

namespace App\EventSubscriber;

use App\Event\PromoterWithdrawalRequestEvent;
use App\Service\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PromoterWithdrawalRequestSubscriber implements EventSubscriberInterface
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onPromoterWithdrawalIsRequested(PromoterWithdrawalRequestEvent $event): void
    {

        $withdrawalRequest = $event->getWithdrawalRequest();

        $withdrawalAmount = $withdrawalRequest->getAmount();

        $promoter = $withdrawalRequest->getPromoter();

        $currentBalance = $promoter->getBalance();

        $promoter->setBalance($currentBalance - $withdrawalAmount);

        $format = new \NumberFormatter("fr_FR", \NumberFormatter::CURRENCY);

        $format->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $amount = $format->formatCurrency($withdrawalAmount, "TND");

        $fullName =  ucfirst($promoter->getFullName());

        $email = (new TemplatedEmail())
            ->from(new Address('noreply@paramall.tn', 'Paramall'))
            ->to(Mailer::PROMOTER_EMAIL)
            ->cc("mrbileltn@gmail.com")
            ->subject(sprintf("Demande de retrait de %s de %s",  $amount, $fullName))
            ->htmlTemplate('email//admin/withdrawal_request.html.twig')
            ->context([
                'promoter' => $promoter,
                'amount' => $amount,
                'withdrawalRequest' => $withdrawalRequest
            ]);

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents()
    {
        return [
            PromoterWithdrawalRequestEvent::EVENT_NAME => 'onPromoterWithdrawalIsRequested',
        ];
    }
}
