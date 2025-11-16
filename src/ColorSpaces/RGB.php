<?php

namespace SocialDept\ColorScales\ColorSpaces;

class RGB
{
    public static function toColor(int|float $r, int|float $g, int|float $b): Color
    {
        $oklch = OKLCH::fromRgb($r, $g, $b);

        return Color::fromOklch($oklch['l'], $oklch['c'], $oklch['h']);
    }

    public static function fromColor(Color $color): array
    {
        $rgb = OKLCH::toRgb($color->l, $color->c, $color->h);

        return [
            'r' => (int) round(max(0, min(255, $rgb['r']))),
            'g' => (int) round(max(0, min(255, $rgb['g']))),
            'b' => (int) round(max(0, min(255, $rgb['b']))),
        ];
    }

    public static function toString(array $rgb): string
    {
        return sprintf('rgb(%d, %d, %d)', $rgb['r'], $rgb['g'], $rgb['b']);
    }
}
