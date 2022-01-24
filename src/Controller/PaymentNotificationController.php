<?php

namespace App\Controller;

use App\Repository\PayementTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        EntityManagerInterface $em
    ): Response {

        $data = $request->request->all();


        if (isset($data["Signature"])) {

            $signature = $data['Signature'];

            $payment = $repo->findOneBy(["ref" => $signature]);

            if ($payment) {

                $payment->setData($data)->setUpdatedAt(new \DateTimeImmutable());

                $em->persist($payment);

                $em->flush();
            } else {
                 
                $data["fail"] = sprintf("payment ref not found width %s", $signature);
            }
        } else {

            $data["fail"] = "no Signature";
        }


        $decoded = json_encode($data, JSON_UNESCAPED_UNICODE);

        $email = (new Email())
            ->to("mrbileltn@gmail.com")
            ->subject('Time for Symfony Mailer!')
            ->text($decoded)
            ->html(sprintf("<p> %s </p>", $decoded));

        $mailer->send($email);

        return $this->json(['property' => 'value'], 200);
    }
}
