<?php

namespace App\Twig;

use App\Entity\Product;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('pricePercentage', [$this, 'pricePercentage']),
        ];
    }

    public function pricePercentage(Product $product): float
    {
        $discounct = $product->getDiscount();

        $price = $product->getPrice();

        if($discounct) {

            $percentage = $discounct->getPourcentage();

            $discountValue = ($price * $percentage) / 100;

            return $price - $discountValue;
        }

        return $price;
    }
}
