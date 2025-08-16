<?php

namespace App\Service\Aramex;

use Symfony\Component\Serializer\SerializerInterface;

class AramexTracking extends Aramex
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    public const TRACKING_WSDL = "http://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc?wsdl";


    public function client(): \SoapClient
    {
        return new \SoapClient(self::TRACKING_WSDL, [
            'exceptions' => 1,
            'trace' => 1
        ]);
    }

    /**
     * @return AramexTrackingEntity[]
     * @deprecated use @method track instead
     */

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

            $results = [];

            $HasMultipleResults = AramexHelper::isMultiDimensional($trackingResults);

            if (!$HasMultipleResults) {

                $results[0] = $trackingResults;
            } else {

                $results = $trackingResults;
            }

            $entities = [];

            foreach ($results as $result) {

                $trackingResult = (array) $result["Value"]["TrackingResult"];

                // if tracking has multiple events api return an array

                $HasMultipleTrackingResult = AramexHelper::isMultiDimensional($trackingResult);

                if ($HasMultipleTrackingResult) {

                    // get the last event

                    $trackingResult = $trackingResult[0];
                }

                $entity = $this->serializer->deserialize(json_encode($trackingResult), AramexTrackingEntity::class, "json");

                array_push($entities, $entity);
            }


            return $entities;
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }

    /**
     * @return AramexTrackingEntity[]
     */

    public function track(array $trackingIds)
    {
        return $this->trackMultiple($trackingIds);
    }


    public function trackOne(string $trackingId): AramexTrackingEntity
    {
        $result = $this->track([$trackingId]);

        return $result[0];
    }

    /**
     * Since  we can not get a full tracking result immediately after the shippement creation
     * We init a tracking to follow next
     */

    public static function init(string $trackingId): array
    {

        $data = [
            "Comments" => "",
            "UpdateCode" => AramexTrackingUpdateCodeResolver::INITIAL_STATUS,
            "WeightUnit" => "KG",
            "GrossWeight" => "0.5",
            "ProblemCode" => "",
            "WaybillNumber" => $trackingId,
            "UpdateDateTime" => null,
            "UpdateLocation" => "Tunis, Tunisia",
            "ChargeableWeight" => "0.5",
            "UpdateDescription" => "Record created."
        ];

        return $data;
    }
}
