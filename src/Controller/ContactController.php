<?php

namespace App\Controller;


use App\Service\Mailer\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{


   /**
    * @Route("/contact", name="contact_api", methods={"POST"})
    */

   public function contact(Request $request, MailerInterface $mailer): Response
   {

      $data = $request->toArray();

      $email = (new TemplatedEmail())
         ->to(Mailer::CONTACT_EMAIL)
         ->cc("im@bilel.dev")
         ->subject("Demande de contact")
         ->htmlTemplate("email/contact/contact.html.twig")
         ->context(["data" => $data]);

      $mailer->send($email);

      return new JsonResponse($data);
   }
}
