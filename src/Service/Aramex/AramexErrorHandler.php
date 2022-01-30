<?php

namespace App\Service\Aramex;

class AramexErrorHandler
{

    public static function getErrorMessage(array $response): string
    {


        if (isset($response["Notifications"]) && count($response["Notifications"])) {

            $errorNotification = $response["Notifications"]["Notification"];

            // api can send multiple error

            $key = array_key_first($errorNotification);

            if (is_array($errorNotification[$key])) {

                //$errorNumber = count($errorNotification);

                return $errorNotification[$key]["Message"];
            } else {

                return $errorNotification["Message"];
            }
        } elseif (isset($response["Shipments"]["ProcessedShipment"]["Notifications"])) {

            $notifications = $response["Shipments"]["ProcessedShipment"]["Notifications"]["Notification"];

            if (AramexHelper::isMultiDimensional($notifications) === false) {

                return $notifications["Message"];
            }

            $errors = implode(', ', array_map(function ($entry) {
                return $entry['Message'] ?? "Unknown error";
            }, $notifications));

            return $errors;
        } else {

            return "Unknown error";
        }
    }
}
