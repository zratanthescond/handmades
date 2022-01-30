<?php

namespace App\Service\Aramex;

class AramexTrackingUpdateCodeResolver
{

    public const STATUS = [

        "SH001" => "Received at Operations Facility",
        "SH002" => "Received at operations facility.",
        "SH003" => "Out for Delivery",
        "SH004" => "Out for Delivery - Partial",
        "SH005" => "Delivered",
        "SH006" => "Collected by Consignee",
        "SH007" => "Will be Delivered by Postal Services",
        "SH008" => "Shipment on Hold",
        "SH011" => "Picked up",
        "SH012" => "Picked Up From Shipper",
        "SH014" => "Record created",
        "SH017" => "N/A",
        "SH024" => "N/A",
        "SH030" => "Still on hold",
        "SH033" => "Attempted Delivery - Delivery Rescheduled for Next Business day",
        "SH034" => "Documents Delivered to Consignee/Broker for Self Clearance & Delivery",
        "SH035" => "Awaiting Clearance from Consignee/Broker to Arrange Delivery",
        "SH041" => "Cleared from Customs",
        "SH043" => "Delay - Delivery Rescheduled for Next Business Day",
        "SH044" => "Delay - Delivery Rescheduled",
        "SH047" => "Received at Origin Facility",
        "SH069" => "To be Returned to Shipper",
        "SH070" => "Redirected to New Delivery Address",
        "SH073" => "Shipment Forwarded to Beyond/Remote Area Sorting Location",
        "SH076" => "Situation Update",
        "SH077" => "Forwarded to Aramex office",
        "SH110" => "Forwarded to Delivery Office",
        "SH154" => "Delivered - Partial Delivery",
        "SH156" => "Held in Customs - Pending Clearance",
        "SH157" => "Unable to Forward - Please Contact your Local Office",
        "SH158" => "Held in Customs - Pending Clearance",
        "SH160" => "Received at operations Facility.",
        "SH162" => "On Hold - Awaiting Customer feedback",
        "SH163" => "Delivery problem - Please contact local office.",
        "SH164" => "Held for Consignee Pickup",
        "SH495" => "Shipment Scanned at Operations Facility",
        "SH496" => "Shipment Picked up by Consignee",
        "SH515" => "Courier Called Customer – Responded",
        "SH516" => "Customer Called Courier - Responded",
        "SH521" => "Upon Consignee Request At Local Office - Shipment To Be Prepared For Collection"

    ];

    public const DELIVERED_STATUS = "SH005";

    public static function getStatus(string $status): string
    {

        if (isset(self::STATUS[$status])) {

            return self::STATUS[$status];
        }

        return "UNKNOW STATUS PLEASE CONTACT ARAMEX OFFICE";
    }
}
