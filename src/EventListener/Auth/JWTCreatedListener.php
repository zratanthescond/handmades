<?php

namespace App\EventListener\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
       
        $payload = $event->getData();

        $user = $event->getUser();

        $payload["firstName"] = $user->getFirstName();

        $payload["fullName"] = $user->getFullName();

        $event->setData($payload);
        
    }
}