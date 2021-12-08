<?php

namespace App\EventSubscriber;

use App\Entity\AramexShipement;
use App\Event\OrderIsPlacedEvent;
use App\Repository\UserAddressRepository;
use App\Service\Aramex\Aramex;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class AramexOrderSubscriber implements EventSubscriberInterface
{

    private Aramex $aramexApi;

    private UserAddressRepository $adressRepo;

    private HttpClientInterface $httpClient;

    private ParameterBagInterface $parameterBag;

    public function __construct(
        Aramex $aramexApi,
        UserAddressRepository $adressRepo,
        HttpClientInterface $httpClient,
        ParameterBagInterface $paramaterBag
    ) {
        $this->aramexApi = $aramexApi;

        $this->adressRepo = $adressRepo;

        $this->httpClient = $httpClient;

        $this->parameterBag = $paramaterBag;
    }


    public function onAramexOrder(OrderIsPlacedEvent $event)
    {

        $order = $event->getOrder();

        $isAramex = $order->getDelivery()->getName() === "Aramex";

        if ($isAramex) {

            $userAddress = $this->adressRepo->findOneBy(["user" => $order->getUser(), "isDefault" => true]);

            $shipement = $this->aramexApi->CreateShipments($userAddress);

            /// save attachement in local storage

            $attachement = $shipement->getShipmentAttachment();

            $attachementName = basename($attachement);

            $path = $this->parameterBag->get('kernel.project_dir') . "/public/upload/aramex/" . $attachementName;

            $response = $this->httpClient->request('GET', $attachement);

            file_put_contents($path, $response->getContent());

            $aramexShipement = (new AramexShipement())
                ->setTrackingId($shipement->getTrackingId())
                ->setAttachement($attachementName);

            $order->setAramexShipement($aramexShipement);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderIsPlacedEvent::EVENT_NAME => ["onAramexOrder", 90]
        ];
    }
}
