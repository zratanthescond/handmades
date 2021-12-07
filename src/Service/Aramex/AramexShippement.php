<?php

namespace App\Service\Aramex;

class AramexShippement
{

    private string $trackingId;

    private string $shipmentAttachment;


    public function __construct(array $Shipments)
    {
        $this->trackingId = $Shipments["ProcessedShipment"]["ID"];

        $this->shipmentAttachment = $Shipments["ProcessedShipment"]["ShipmentLabel"]["LabelURL"];
    }

    /**
     * Get the value of trackingId
     */
    public function getTrackingId(): string
    {
        return $this->trackingId;
    }


    /**
     * Get the value of shipmentAttachment
     */
    public function getShipmentAttachment(): string
    {
        return $this->shipmentAttachment;
    }
}
