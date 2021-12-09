<?php

namespace App\Controller\ApiOperation\Parrainage;

use App\Entity\Parrainage;
use App\Event\NewParrainageEvent;
use App\Repository\ParrainageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class NewParrainageController extends AbstractController
{

    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        UserRepository $userRepo,
        ParrainageRepository $parrainageRepo,
        EventDispatcherInterface $eventDispatcher
    ) {

        $data = $request->toArray();

        $parrainage = $serializer->deserialize(json_encode($data), Parrainage::class, "json");

        $isAlreadyDone = $parrainageRepo->findOneBy(["beneficiaryEmail" => $parrainage->getBeneficiaryEmail()]);

        $isSubscribed = $userRepo->findOneBy(["email" => $parrainage->getBeneficiaryEmail()]);

        if($isSubscribed) {

            return $this->json(["error" => "Ce client est déja inscrit"], 400);
        }

        if ($isAlreadyDone) {

            return $this->json(["error" => "Un parrainage a été déja effectué avec cette adresse email"], 400);
        }

        $user = $userRepo->find($data["userId"]);

        $parrainage->setFromUser($user)->setToken(base64_encode($parrainage->getBeneficiaryEmail()));

        $em = $this->getDoctrine()->getManager();

        $em->persist($parrainage);

        $em->flush();

        $event = new NewParrainageEvent($parrainage);

        $eventDispatcher->dispatch($event, NewParrainageEvent::EVENT_NAME);

        return $this->json($data);
    }
}
