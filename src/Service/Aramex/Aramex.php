<?php

namespace App\Service\Aramex;

use App\Entity\User;
use App\Entity\UserAddress;
use App\Service\Aramex\Exception\AramexException;

class Aramex
{

    // URL LIVE "https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl

    public const WSDL = "https://ws.dev.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl";

    protected $compagnyName;

    protected $compagnyAddress;

    protected $compagnyCity;

    protected $compagnyPhone;

    protected $compagnyEmail;


    public function __construct(
        $compagnyName,
        $compagnyAddress,
        $compagnyCity,
        $compagnyPhone,
        $compagnyEmail
    ) {

        $this->compagnyName = $compagnyName;

        $this->compagnyAddress = $compagnyAddress;

        $this->compagnyCity = $compagnyCity;

        $this->compagnyPhone = $compagnyPhone;

        $this->compagnyEmail = $compagnyEmail;
    }

    public function client(): \SoapClient
    {
        return new \SoapClient(self::WSDL, [
            'exceptions' => 1,
            'trace' => 1
        ]);
    }

 
    public function CreateShipments(UserAddress $userAddress): AramexShippement
    {
        $soapClient = $this->client();

        $user = $userAddress->getUser();

        //$soapClient->__getFunctions());

        $params = array(
            'Shipments' => array(
                'Shipment' => array(
                    'Shipper'    => array(
                        'Reference1'     => 'Ooption', //option ref a
                        'Reference2'     => 'Ref 222222', // ref user creadted
                        'AccountNumber' => '20016', //numer de compt aramex
                        'PartyAddress'    => array( //addreess de envo
                            'Line1'                    => $this->compagnyAddress, //address physique de envo
                            'Line2'                 => '',
                            'Line3'                 => '',
                            'City'                    => $this->compagnyCity, // vilee de env (from list a enc=ve)
                            'StateOrProvinceCode'    => '',
                            'PostCode'                => '',
                            'CountryCode'            => 'TN'
                        ),
                        'Contact'        => array( // none de env
                            'Department'            => '',
                            'PersonName'            =>  $this->compagnyName . " , "  . $this->compagnyAddress, //nome vendeur
                            'Title'                    => '',
                            'CompanyName'            => $this->compagnyName . " , "  . $this->compagnyAddress, //nome vendeur
                            'PhoneNumber1'            => $this->compagnyPhone, // numer tel vendeur
                            'PhoneNumber1Ext'        => '',
                            'PhoneNumber2'            => '',
                            'PhoneNumber2Ext'        => '',
                            'FaxNumber'                => '',
                            'CellPhone'                => $this->compagnyPhone, //oblig numer de tel 
                            'EmailAddress'            => $this->compagnyEmail, // mail address obliga
                            'Type'                    => ''
                        ),
                    ),

                    'Consignee'    => array( //reciver
                        'Reference1'    => 'Ref 333333', //option
                        'Reference2'    => 'Ref 444444', //option 
                        'AccountNumber' => '',
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
                        'Services'                => '', //"CODS" PAMENT A LIVRE ,"RTRN" ECHANGE ,"RTRN,CODS" echange + payment a livraisent ; (vide)livraisent normal sans payment san echange 
                        'NumberOfPieces'        => 1, // number de embalage general a expdi 
                        'DescriptionOfGoods'     => 'Docs',
                        'GoodsOriginCountry'     => 'TN',

                        'CashOnDeliveryAmount'     => array( //IF CODS IN SERVICE 
                            'Value'                    => 0, // amount will be collected 10
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
                'AccountCountryCode'    => 'JO', //TN LIVE
                'AccountEntity'             => 'AMM', //TUN LIVE
                'AccountNumber'             => '20016',
                'AccountPin'             => '331421',
                'UserName'                 => 'reem@reem.com',
                'Password'                 => '123456789',
                'Version'                 => '1.0'
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

                throw new AramexException("Une erreur est survenue");
            }

            return new AramexShippement($data["Shipments"]);
        } catch (\SoapFault $fault) {

            dd('Error : ' . $fault->faultstring);
        }
    }
}
