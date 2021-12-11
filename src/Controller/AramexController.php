<?php

namespace App\Controller;

use App\Repository\UserAddressRepository;
use App\Repository\UserRepository;
use App\Service\Aramex\Aramex;
use App\Service\Aramex\AramexPickUp;
use App\Service\Aramex\Exception\AramexException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class AramexController extends AbstractController
{
     
 

    /**
     * @Route("/aramex", name="aramex")
     */

    public function test(AramexPickUp $api, MailerInterface $mailer)
    {

      
      $email = new TemplatedEmail();

      $email->to("mrbileltn@gmail.com")
            ->subject("hello")
            ->htmlTemplate("email/hello.html.twig");
     
            $mailer->send($email);

            dd($email);
      
    }
}