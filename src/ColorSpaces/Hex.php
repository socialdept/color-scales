<?php

namespace SocialDept\ColorScales\ColorSpaces;

class Hex
{
    public static function toColor(string $hex): Color
    {
        $hex = str_replace('#', '', $hex);

        // Handle shorthand hex (e.g., "fff" ’ "ffffff")
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return RGB::toColor($r, $g, $b);
    }

    public static function fromColor(Color $color): string
    {
        $rgb = RGB::fromColor($color);

        return sprintf('#%02x%02x%02x', $rgb['r'], $rgb['g'], $rgb['b']);
    }
}
