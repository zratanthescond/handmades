<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\UserAddress;
use Doctrine\ORM\EntityManagerInterface;

class ClientAddressPersister implements DataPersisterInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports($data): bool
    {
        return $data instanceof UserAddress;
    }

    public function persist($data)
    {
        $repo = $this->em->getRepository(UserAddress::class);

        $user = $data->getUser();

        $addressesCount = $repo->countByUser($user);

        // handle default addresse

       if($addressesCount == 0) {

              $data->setIsDefault(true);
        
       } elseif($data->getIsDefault()) {

             $adresses = $repo->findAll();

             foreach($adresses as $address) {

                  $address->setIsDefault(false);

                  $this->em->persist($address);
             }

             $data->setIsDefault(true);
        }
        
        $this->em->persist($data);

        $this->em->flush();
    }

    public function remove($data)
    {
       
        $this->em->remove($data);
        $this->em->flush();
    }


}