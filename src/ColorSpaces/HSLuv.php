<?php

namespace SocialDept\ColorScales\ColorSpaces;

use HSLuv\HSLuv as HsluvConverter;

/**
 * HSLuv color space conversions.
 * HSLuv is a perceptually uniform color space.
 */
class HSLuv
{
    /**
     * Convert HSLuv to Color (OKLCH).
     */
    public static function toColor(float $h, float $s, float $l): Color
    {
        // HSLuv uses 0-360 for hue, 0-100 for saturation and lightness
        // Convert to hex first, then to Color
        $hex = HsluvConverter::toHex($h, $s, $l);

        // Convert hex to OKLCH via RGB
        return Hex::toColor($hex);
    }

    /**
     * Convert Color (OKLCH) to HSLuv.
     */
    public static function fromColor(Color $color): array
    {
        $hex = Hex::fromColor($color);

        // Convert hex to HSLuv array [h, s, l]
        $hsluv = HsluvConverter::fromHex($hex);

        return [
            'h' => $hsluv[0] ?? 0, // Handle potential NaN for grayscale
            's' => $hsluv[1] ?? 0,
            'l' => $hsluv[2] ?? 0,
        ];
    }

    /**
     * Get HSLuv string representation.
     */
    public static function toString(float $h, float $s, float $l): string
    {
        return sprintf('hsluv(%.1f, %.1f%%, %.1f%%)', $h, $s, $l);
    }
}
