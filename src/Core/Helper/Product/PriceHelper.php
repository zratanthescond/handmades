<?php

namespace App\Core\Helper\Product;

use App\Entity\Product;

class PriceHelper
{

    public static function appendDiscountDetails(Product $product): Product
    {

        $price = $product->getPrice();

        $discount = $product->getDiscount();

        $discountValue = $price * $discount->getPourcentage() / 100;

        $discount->setNewPrice(round($price - $discountValue));

        if (!$discount->getExpireAt()) {

            $discount->setIsExpired(false);
        }


       return $product;
    }
}
