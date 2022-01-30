<?php


namespace App\Service\Aramex;

use App\Entity\Order;
use App\Service\Aramex\Exception\AramexException;

class AramexPickUp extends Aramex
{

    use AramexHelper;

    public function createPickUp(\DateTime $readyTime, \DateTime $lastPickupTime): ProcessedPickup
    {

        $params = array(

            // agence 101 col a exp 
            'Pickup' => array(
                'PickupContact' => array(
                    'PersonName' => $this->compagnyName . " , "  . $this->compagnyAddress, // buyer name (how we will picked from him the shippment)
                    'CompanyName' => $this->compagnyName . " , "  . $this->compagnyAddress, // buyer name (how we will picked from him the shippment)
                    'PhoneNumber1' => $this->compagnyPhone, // phone  number  of buyer
                    'PhoneNumber1Ext' => '',
                    'CellPhone' => $this->compagnyPhone, //phone  number  of buyer
                    'EmailAddress' => $this->compagnyEmail, // buyer mail addres
                ),
                'PickupAddress' => array(
                    'Line1' => $this->compagnyName . " , "  . $this->compagnyAddress, //buyer address
                    'City' => $this->compagnyCity, // buyer city
                    'StateOrProvinceCode' => '', //optionel
                    'PostCode' => '', //optionel
                    'CountryCode' => 'TN',
                ),
                'PickupLocation' => 'reciption ', // keep it static lik "reciption"
                'PickupDate' => $readyTime->getTimestamp(), // date requested for the pickup  avant 11 am le mm jour  apres 11 am j +1 2021-12-0T08:00:00.000Z
                'ReadyTime' => $readyTime->getTimestamp(), // time requested for the pickup ready time ouvert 9 am a 7 pm 
                'LastPickupTime' => $lastPickupTime->getTimestamp(), // time requested for the pickup last time  last tim 7pm
                'ClosingTime' => $lastPickupTime->getTimestamp(), // time requested for the pickup last time
                'Comments' => 'test', //optionel
                'Reference1' => 'test', //optionel
                'Reference2' => '', //optionel
                'Vehicle' => 'test', //optionel
                'PickupItems' => array(
                    'PickupItemDetail' => array(
                        'ProductGroup' => 'DOM',
                        'ProductType' => 'ONP', //smp
                        'NumberOfShipments' => 1, // boc number de couli a expdie option 
                        'PackageType' => '',
                        'Payment' => 'P',
                        'ShipmentWeight'        => array(
                            'Value'        => 0.5,
                            'Unit'        => 'Kg',
                        ),
                        'ShipmentVolume' => array(
                            'Value'        => 0.5,
                            'Unit'        => 'Kg',
                        ),
                        'NumberOfPieces' => '1', //option 

                        'CashAmoun'     => array( // SI SERVICE = CODS, CODS,RTRN 
                            'Value'                    => 0, //SOME DE PRIX DEXPIDTION  3 PANEE 100 107
                            'CurrencyCode'            => 'TND'
                        ),

                        'ExtraCharges'     => array( // SI SERVICE = CODS, CODS,RTRN 
                            'Value'                    => 10, //SOME DE PRIX DEXPIDTION  3 PANEE 100 107
                            'CurrencyCode'            => 'TND'
                        ),
                        'ShipmentDimensions' => array(
                            'Length'                => 1,
                            'Width'                    => 10,
                            'Height'                => 10,
                            'Unit'                    => 'cm',

                        ),

                        'ShipmentDimensions' => array(

                            'Length'                => 10,
                            'Width'                    => 10,
                            'Height'                => 10,
                            'Unit'                    => 'cm',

                        ),


                    ),
                ),
                'Status' => 'READY',
                'Comments' => '',


            ),

            'ClientInfo'              => array(
                'AccountCountryCode'    => 'TN', //TN LIVE
                'AccountEntity'             => AramexConfig::ACCOUNT_ENTITY, //TUN LIVE
                'AccountNumber'             => AramexConfig::ACCOUNT_NUMBER,
                'AccountPin'             => AramexConfig::ACCOUNT_PIN,
                'UserName'                 => AramexConfig::USERNAME,
                'Password'                 => AramexConfig::PASSWORD,
                'Version'                 => AramexConfig::VERSION,
            ),

            'Transaction'             => array(
                'Reference1'            => '001',
                'Reference2'            => '',
                'Reference3'            => '',
                'Reference4'            => '',
                'Reference5'            => '',
            ),
            'LabelInfo'                => array(
                'ReportID'                 => 9201,
                'ReportType'            => 'URL',
            ),
        );

        $params['Shipments']['Shipment']['Details']['Items'][] = array(
            'PackageType'     => 'Box',
            'Quantity'        => 1,
            'Weight'        => array(
                'Value'        => 0.5,
                'Unit'        => 'Kg',
            ),
            'Comments'        => 'Docs',
            'Reference'        => ''
        );

        try {

            $soapClient = $this->client();

            $res = $soapClient->CreatePickup($params);

            $data = $this->normalize($res);

            ///dd($data);

            if ($data["HasErrors"] === true) {

                $errorMessage = AramexErrorHandler::getErrorMessage($data);

                throw new AramexException($errorMessage);
            }

            return new ProcessedPickup($data["ProcessedPickup"]);

            $soap_request = $soapClient->__getLastRequest();
        } catch (\SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }
}
