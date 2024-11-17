<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeConversionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('decimal_to_time', [$this, 'decimalToTime']),
        ];
    }

    public function decimalToTime(float $decimalHours): string
    {
        $hours   = \floor($decimalHours);
        $minutes = \round(($decimalHours - $hours) * 60);

        return \sprintf('%02d:%02d', $hours, $minutes);
    }
}
