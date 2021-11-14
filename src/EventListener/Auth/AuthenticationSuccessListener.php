<?php

namespace App\EventListener\Auth;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Serializer\SerializerInterface;

class AuthenticationSuccessListener
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {

        $data = $event->getData();

        $user = $event->getUser();

        if(!$user instanceof User) {

              return;
        }

        
        $decodedWishList = json_decode($this->serializer->serialize($user->getWishList(), "json", ["groups" => ["user:read"]]));

        $wishList = [];

        foreach($decodedWishList as $el) {

             array_push($wishList, "/api/products/{$el->id}");
        }

        $data["userData"] = [

              "firstName" => ucfirst($user->getFirstName()),
              "id" => $user->getId(),
              "fullName" => $user->getFullName(),
              "wishList" => $wishList
        ];

        $event->setData($data);
    }
}