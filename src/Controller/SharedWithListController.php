<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class SharedWithListController extends AbstractController
{

    /**
     * @Route("/share/wishlist", name="share_wishlist_api", methods={"POST"})
     */

    public function index(Request $request, MailerInterface $mailer, UserRepository $userRepo)
    {

        $data = $request->toArray();

        $userId = $data["userId"];

        $recipientEmail = $data["recipientEmail"];

        $user = $userRepo->find($userId);

        $email = (new TemplatedEmail())
        ->to($recipientEmail)
        ->subject(sprintf("%s souhaite partager avec vous son wishlist", ucfirst($user->getFirstName())))
        ->htmlTemplate("email/wishlist/shared.html.twig")
        ->context([
            "data" => $data,
            "user" => $user
         ]);

        $mailer->send($email);
        
        return new JsonResponse($data);



    }
}