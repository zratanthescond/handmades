<?php

namespace App\Service\Aramex;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\UserAddress;
use App\Service\Aramex\Exception\AramexException;

class Aramex
{

    // URL LIVE "https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl

    // url dev https://ws.dev.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl

    public const WSDL = "https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl";

    public function client(): \SoapClient
    {
        return new \SoapClient(self::WSDL, [
            'exceptions' => 1,
            'trace' => 1
        ]);
    }

 
    public function CreateShipments(UserAddress $userAddress, Order $order): AramexShippement
    {
        $soapClient = $this->client();

        $user = $userAddress->getUser();

        //$soapClient->__getFunctions());

        $cashOnDelivery = $order->getPayementTransaction() === null;

        

        $params = array(
            'Shipments' => array(
                'Shipment' => array(
                    'Shipper'    => array(
                        'Reference1'     => '', //option ref a
                        'Reference2'     => '', // ref user creadted
                        'AccountNumber' => AramexConfig::ACCOUNT_NUMBER, //numer de compt aramex
                        'PartyAddress'    => array( //addreess de envo
                            'Line1'                    => AramexConfig::COMPAGNY_ADDRESS, //address physique de envo
                            'Line2'                 => '',
                            'Line3'                 => '',
                            'City'                    => AramexConfig::COMPAGNY_CITY, // vilee de env (from list a enc=ve)
                            'StateOrProvinceCode'    => '',
                            'PostCode'                => '',
                            'CountryCode'            => 'TN'
                        ),
                        'Contact'        => array( // none de env
                            'Department'            => '',
                            'PersonName'            =>  AramexConfig::COMPAGNY_NAME. " , "  . AramexConfig::COMPAGNY_ADDRESS, //nome vendeur
                            'Title'                    => '',
                            'CompanyName'            => AramexConfig::COMPAGNY_NAME . " , "  . AramexConfig::COMPAGNY_ADDRESS, //nome vendeur
                            'PhoneNumber1'            => AramexConfig::COMPAGNY_PHONE, // numer tel vendeur
                            'PhoneNumber1Ext'        => '',
                            'PhoneNumber2'            => '',
                            'PhoneNumber2Ext'        => '',
                            'FaxNumber'                => '',
                            'CellPhone'                => AramexConfig::COMPAGNY_PHONE, //oblig numer de tel 
                            'EmailAddress'            => AramexConfig::COMPAGNY_EMAIL, // mail address obliga
                            'Type'                    => ''
                        ),
                    ),

                    'Consignee'    => array( //reciver
                        'Reference1'    => 'Ref 333333', //option
                        'Reference2'    => 'Ref 444444', //option 
                        'AccountNumber' => '60519122',
                        'PartyAddress'    => array(
                            'Line1'                    => $userAddress->getAddress(), //addrerss destinater
                            'Line2'                    => '',
                            'Line3'                    => '',
                            'City'                    => $userAddress->getTown(), /// vile de reciveur 
                            'StateOrProvinceCode'    => '',
                            'PostCode'                => '',
                            'CountryCode'            => 'tn'
                        ),

                        'Contact'        => array(
                            'Department'            => '',
                            'PersonName'            => $user->getFullName(), // nome client 
                            'Title'                    => '',
                            'CompanyName'            => $user->getFullName(), // nbome client 
                            'PhoneNumber1'            => $user->getPhoneNumber(), // numer de tel reciveur
                            'PhoneNumber1Ext'        => '',
                            'PhoneNumber2'            => '',
                            'PhoneNumber2Ext'        => '',
                            'FaxNumber'                => '',
                            'CellPhone'                => $user->getPhoneNumber(), //numer de tel reciveur
                            'EmailAddress'            => $user->getEmail(), // mail address 
                            'Type'                    => ''
                        ),
                    ),

                    'ThirdParty' => array(
                        'Reference1'     => '',
                        'Reference2'     => '',
                        'AccountNumber' => '',
                        'PartyAddress'    => array(
                            'Line1'                    => '',
                            'Line2'                    => '',
                            'Line3'                    => '',
                            'City'                    => '',
                            'StateOrProvinceCode'    => '',
                            'PostCode'                => '',
                            'CountryCode'            => ''
                        ),
                        'Contact'        => array(
                            'Department'            => '',
                            'PersonName'            => '',
                            'Title'                    => '',
                            'CompanyName'            => '',
                            'PhoneNumber1'            => '',
                            'PhoneNumber1Ext'        => '',
                            'PhoneNumber2'            => '',
                            'PhoneNumber2Ext'        => '',
                            'FaxNumber'                => '',
                            'CellPhone'                => '',
                            'EmailAddress'            => '',
                            'Type'                    => ''
                        ),
                    ),

                    // Order details
                    'Reference1'                 => 'Shpt 0001', //OPTION ref com
                    'Reference2'                 => '',
                    'Reference3'                 => '',
                    'ForeignHAWB'                => '',
                    'TransportType'                => 0,
                    'ShippingDateTime'             => time(),
                    'DueDate'                    => time(),
                    'PickupLocation'            => 'Reception',
                    'PickupGUID'                => '',
                    'Comments'                    => 'Shpt 0001',
                    'AccountingInstrcutions'     => '',
                    'OperationsInstructions'    => '',

                    'Details' => array(
                        'Dimensions' => array(
                            'Length'                => 1,
                            'Width'                    => 1,
                            'Height'                => 1,
                            'Unit'                    => 'cm',

                        ),

                        'ActualWeight' => array(
                            'Value'                    => 0.5,
                            'Unit'                    => 'Kg'
                        ),

                        'ProductGroup'             => 'DOM',
                        'ProductType'            => 'ONP', // VALEUR = "ONP" pick aramex ( creat pickup  api call ), "FIX" drop buro aramex
                        'PaymentType'            => 'P',
                        'PaymentOptions'         => '',
                        'Services'                => $cashOnDelivery?'CODS':'', //"CODS" PAMENT A LIVRE ,"RTRN" ECHANGE ,"RTRN,CODS" echange + payment a livraisent ; (vide)livraisent normal sans payment san echange 
                        'NumberOfPieces'        => 1, // number de embalage general a expdi 
                        'DescriptionOfGoods'     => 'Docs',
                        'GoodsOriginCountry'     => 'TN',

                        'CashOnDeliveryAmount'     => array( //IF CODS IN SERVICE 
                            'Value'                    => $cashOnDelivery ? $order->getTotal() : '', // amount will be collected 
                            'CurrencyCode'            => 'TND'
                        ),

                        'InsuranceAmount'        => array(
                            'Value'                    => 0,
                            'CurrencyCode'            => ''
                        ),

                        'CollectAmount'            => array(
                            'Value'                    => 0,
                            'CurrencyCode'            => ''
                        ),

                        'CashAdditionalAmount'    => array(
                            'Value'                    => 0,
                            'CurrencyCode'            => ''
                        ),

                        'CashAdditionalAmountDescription' => '',

                        'CustomsValueAmount' => array(
                            'Value'                    => 0,
                            'CurrencyCode'            => ''
                        ),

                        'Items'                 => array()
                    ),
                ),
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
                'ReportID'                 => 9824,
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

            $res = $soapClient->CreateShipments($params);

            $data = json_decode(json_encode($res), true);

            if ($data["HasErrors"] === true) {

               $errorMessage = AramexErrorHandler::getErrorMessage($data);

                throw new AramexException($errorMessage);
            }

            return new AramexShippement($data["Shipments"]);
       
        } catch (\SoapFault $fault) {

            dd('Error : ' . $fault->faultstring);
        }
    }
}
