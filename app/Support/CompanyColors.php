<?php

namespace App\Support;

class CompanyColors
{
    /** A pale tint of the brand colour (90% white), used for tags and icon backgrounds. */
    public static function tone(string $hex): string
    {
        [$r, $g, $b] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');

        $mix = fn (int $c) => (int) round($c + (255 - $c) * 0.9);

        return sprintf('#%02x%02x%02x', $mix($r), $mix($g), $mix($b));
    }
}
