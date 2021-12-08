<?php

namespace App\Service\Aramex;


class ProcessedPickup
{

    private string $id;

    private string $guid;

    public function __construct(array $processedPickup)
    {
        $this->id = $processedPickup["ID"];

        $this->guid = $processedPickup["GUID"];
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }
}
