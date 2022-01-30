<?php

namespace App\Service\Aramex;

class AramexTracking extends Aramex
{

    public const WSDL = "http://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc?wsdl";


    public function client(): \SoapClient
    {
        return new \SoapClient(self::WSDL, [
            'exceptions' => 1,
            'trace' => 1
        ]);
    }


    public function trackMultiple(array $trackingIds): array
    {

        $soapClient = $this->client();

        /*
		parameters needed for the trackShipments method , client info, Transaction, and Shipments' Numbers.
		Note: Shipments array can be more than one shipment.
	*/
        $params = array(
            'ClientInfo' => AramexConfig::getClientInfos(),

            'Transaction' => array(
                'Reference1' => '001'
            ),
            'Shipments' => $trackingIds
        );

        // calling the method and printing results
        try {
            $res = $soapClient->TrackShipments($params);

            $data  = json_decode(json_encode($res), true);

            if (AramexHelper::hasError($data)) {

                throw new AramexException("Unknow error");
            }

            $trackingResults = (array) $data["TrackingResults"][array_key_first($data["TrackingResults"])];

            return $trackingResults;
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }


    public function trackOne(string $trackingId): array
    {

        $tracking = $this->trackMultiple([$trackingId]);

        $trackingResult = (array) $tracking["Value"]["TrackingResult"];

        return $trackingResult;
    }
}
