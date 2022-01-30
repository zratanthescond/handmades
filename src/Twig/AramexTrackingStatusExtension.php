<?php

namespace App\Twig;

use App\Service\Aramex\AramexTrackingUpdateCodeResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AramexTrackingStatusExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('aramexStatus', [$this, 'aramexStatus']),
        ];
    }


    public function aramexStatus($value)
    {
        return AramexTrackingUpdateCodeResolver::getStatus($value);
    }
}
