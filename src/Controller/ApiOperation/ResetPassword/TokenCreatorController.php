<?php

namespace App\Controller\ApiOperation\ResetPassword;

use App\Entity\ResetPassword;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

class TokenCreatorController extends AbstractController
{

    public function __invoke(
        Request $request,
        UserRepository $repo,
        ResetPasswordRepository $resetRepo,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ) {

        $data = $request->toArray();

        $userEmail = $data["userEmail"];

        $user = $repo->findOneBy(["email" => $userEmail]);

        if(!$user) {

            return new JsonResponse(["error" => "Aucun compte associé avec cette adresse email"], 404);
        }

        // check if user has already a token

        $oldUserResetPassword = $user->getResetPassword();

        if($oldUserResetPassword) {

            return new JsonResponse([
            "error" => "Vous avez déja demandé un changement de mot de passe, veuillez vérifier votre adresse email ". $user->getEmail()]
            , 400);
        }
 
        $token = base64_encode(uniqid().$user->getEmail());

        $expiresAt = (new \DateTimeImmutable())->add(new \DateInterval('P1D'));

        $resetPassword = (new ResetPassword)->setUser($user)->setToken($token)->setExpiresAt($expiresAt);

        $em->persist($resetPassword);

        $em->flush();

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject("Changement de votre mot de passe")
            ->context(["user" => $user, "token" => $token])
            ->htmlTemplate("email/reset_password/reset_password_request.html.twig");

        $mailer->send($email);

        return new JsonResponse(["token" => $token]);
    }
}
