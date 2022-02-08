<?php

namespace App\Service\Aramex;

class AramexConfig
{

    public const USERNAME = "zgolli.issam@gmail.com";

    public const PASSWORD = "Issam1982!";

    public const VERSION = "v1";

    public const ACCOUNT_NUMBER = "60525278";

    public const ACCOUNT_PIN = "443543";

    public const ACCOUNT_ENTITY = "TUN";

    public const ACCOUNT_COUNTRY_CODE = "TN";

   public const COMPAGNY_NAME = "Paramall";

   public const COMPAGNY_ADDRESS = "Rue Somaani cité Ennasim Ariana 2073, Tunis";

   public const COMPAGNY_CITY = "Ariana";

   public const COMPAGNY_PHONE = "28 122 180";

   public const COMPAGNY_EMAIL = "contact@paramall.tn";
    

    public static function getClientInfos(): array
    {
        return [

            'AccountCountryCode' => self::ACCOUNT_COUNTRY_CODE,
            'AccountEntity' => self::ACCOUNT_ENTITY,
            'AccountNumber' => self::ACCOUNT_NUMBER,
            'AccountPin' => self::ACCOUNT_PIN,
            'UserName' => self::USERNAME,
            'Password' => self::PASSWORD,
            'Version' => self::VERSION
        ];
    }
}
