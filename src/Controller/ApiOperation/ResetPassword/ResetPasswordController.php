<?php

namespace App\Controller\ApiOperation\ResetPassword;

use App\Repository\ResetPasswordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{

    public function __invoke(
        Request $request,
        ResetPasswordRepository $repo,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em
    ) {


        $data = $request->toArray();

        $token = $data["token"];

        $plainPassword = $data["newPassword"];

        $resetPasswordRequest = $repo->findOneBy(["token" => $token]);

        if (!$resetPasswordRequest) {

            return $this->json(["error" => "Invalid token"], 400);
        }

        $user = $resetPasswordRequest->getUser();

        $newPassword = $encoder->encodePassword($user, $plainPassword);

        $user->setPassword($newPassword);

        $resetPasswordRequest->setUser(null);

        $em->persist($resetPasswordRequest);

        $em->persist($user);

        $em->flush();

        return $this->json(["userName" => $user->getLastName()]);
    }
}
