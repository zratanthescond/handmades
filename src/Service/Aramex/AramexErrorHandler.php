<?php

namespace App\Service\Aramex;

class AramexErrorHandler
{

    public static function getErrorMessage(array $response): string
    {

        $errorNotification = $response["Notifications"]["Notification"];

        // api can send multiple error

        $key = array_key_first($errorNotification);

        if (is_array($errorNotification[$key])) {

            $errorNumber = count($errorNotification);

            return $errorNotification[$key]["Message"];
        }

        return $errorNotification["Message"];
    }
}
