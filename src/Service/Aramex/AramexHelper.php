<?php

namespace App\Service\Aramex;

trait AramexHelper
{
    public function normalize(object $data, bool $associative = true): array
    {

        return json_decode(json_encode($data), $associative);
    }


    public function hasError()
    {
    }
}
