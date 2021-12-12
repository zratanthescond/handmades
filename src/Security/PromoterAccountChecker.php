<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Promoter;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class PromoterAccountChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Promoter) {
            return;
        }

        $isActif = $user->getIsActif();

        if(!$isActif) {

            throw new CustomUserMessageAccountStatusException("Votre compte n'est pas activé");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof Promoter) {
            return;
        }
    }
}