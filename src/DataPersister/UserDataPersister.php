<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements DataPersisterInterface
{

    private $em;

    private $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $this->em = $em;

        $this->hasher = $hasher;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }


    public function persist($data)
    {
        if ($data->getPlainPassword()) {

            $hashed = $this->hasher->hashPassword($data, $data->getPlainPassword());

            $data->setPassword($hashed);

            $data->eraseCredentials();
        }

        $this->em->persist($data);

        $this->em->flush();
    }


    public function remove($data)
    {
    }
}
