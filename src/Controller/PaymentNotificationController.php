<?php

namespace App\Controller;

use App\Event\OrderIsPlacedEvent;
use App\Repository\PayementTransactionRepository;
use App\Service\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;


class PaymentNotificationController extends AbstractController
{

    /**
     * @Route("/payment/notification", name="payment_notification")
     */
    public function index(
        Request $request,
        MailerInterface $mailer,
        PayementTransactionRepository $repo,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    ): Response {

        $data = $request->request->all();

        if (isset($data["PAYID"])) {

            $ref = $data["PAYID"];

            $payment = $repo->findOneBy(["ref" => $ref]);

            if ($payment) {

                $payment->setData($data)->setUpdatedAt(new \DateTimeImmutable());

                $em->persist($payment);

                $em->flush();

                $order = $payment->getCOrder();

                $event = new OrderIsPlacedEvent($order);

                $dispatcher->dispatch($event, OrderIsPlacedEvent::EVENT_NAME);

                $decoded = json_encode($data, JSON_UNESCAPED_UNICODE);

                $email = (new Email())
                    ->to(Mailer::ORDER_EMAIL)
                    ->cc("mrbileltn@gmail.com")
                    ->subject('Payment notification for Order Id: ' . $order->getId())
                    ->text($decoded);

                $mailer->send($email);
            } else {

                $data["fail"] = "can not find payement with this ref" . $ref;
            }
        } else {

            $data["fail"] = "no ref";
        }

        return $this->json(['success' => '200'], 200);
    }
}
