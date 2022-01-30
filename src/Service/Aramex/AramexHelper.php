<?php

namespace App\Service\Aramex;

trait AramexHelper
{
    public function normalize(object $data, bool $associative = true): array
    {

        return json_decode(json_encode($data), $associative);
    }


    public static function hasError(array $data): bool
    {

        return $data["HasErrors"] === true;
    }

    public static function isMultiDimensional(array $data): bool
    {
        $key = array_key_first($data);
        
        return is_array($data[$key]);
    } 
}
