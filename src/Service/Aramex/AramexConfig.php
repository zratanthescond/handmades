<?php

namespace App\Service\Aramex;

class AramexConfig
{

    public const USERNAME = "imedm@aramex.com";

    public const PASSWORD = "Imed+07377717";

    public const VERSION = "v1";

    public const ACCOUNT_NUMBER = "60519122";

    public const ACCOUNT_PIN = "115265";

    public const ACCOUNT_ENTITY = "TUN";

    public const ACCOUNT_COUNTRY_CODE = "TN";

    public static function getClientInfos(): array
    {
        return [

            'AccountCountryCode'    => self::ACCOUNT_COUNTRY_CODE,
            'AccountEntity'   => self::ACCOUNT_ENTITY,
            'AccountNumber'  => self::ACCOUNT_NUMBER,
            'AccountPin'  => self::ACCOUNT_PIN,
            'UserName'     => self::USERNAME,
            'Password'   => self::PASSWORD,
            'Version'  => self::VERSION
        ];
    }

}