<?php

namespace SocialDept\ColorScales\ColorSpaces;

/**
 * OKLCH color space converter.
 * Handles conversion between sRGB and OKLCH via XYZ and OKLab.
 */
class OKLCH
{
    /**
     * Convert OKLCH to sRGB (0-255 range).
     */
    public static function toRgb(float $l, float $c, float $h): array
    {
        // OKLCH ’ OKLab
        $hRad = deg2rad($h);
        $a = $c * cos($hRad);
        $b = $c * sin($hRad);

        // OKLab ’ Linear RGB (via matrix transformation)
        $l_ = $l + 0.3963377774 * $a + 0.2158037573 * $b;
        $m_ = $l - 0.1055613458 * $a - 0.0638541728 * $b;
        $s_ = $l - 0.0894841775 * $a - 1.2914855480 * $b;

        $l = $l_ * $l_ * $l_;
        $m = $m_ * $m_ * $m_;
        $s = $s_ * $s_ * $s_;

        $r = +4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s;
        $g = -1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s;
        $b = -0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s;

        // Linear RGB ’ sRGB (gamma correction)
        $r = self::gammaCorrection($r);
        $g = self::gammaCorrection($g);
        $b = self::gammaCorrection($b);

        return [
            'r' => $r * 255,
            'g' => $g * 255,
            'b' => $b * 255,
        ];
    }

    /**
     * Convert sRGB (0-255) to OKLCH.
     */
    public static function fromRgb(int|float $r, int|float $g, int|float $b): array
    {
        // Normalize to 0-1
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;

        // sRGB ’ Linear RGB (inverse gamma)
        $r = self::inverseGammaCorrection($r);
        $g = self::inverseGammaCorrection($g);
        $b = self::inverseGammaCorrection($b);

        // Linear RGB ’ OKLab (via matrix transformation)
        $l = 0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b;
        $m = 0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b;
        $s = 0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b;

        $l_ = pow($l, 1 / 3);
        $m_ = pow($m, 1 / 3);
        $s_ = pow($s, 1 / 3);

        $L = 0.2104542553 * $l_ + 0.7936177850 * $m_ - 0.0040720468 * $s_;
        $a = 1.9779984951 * $l_ - 2.4285922050 * $m_ + 0.4505937099 * $s_;
        $b = 0.0259040371 * $l_ + 0.7827717662 * $m_ - 0.8086757660 * $s_;

        // OKLab ’ OKLCH
        $c = sqrt($a * $a + $b * $b);
        $h = atan2($b, $a) * 180 / pi();

        if ($h < 0) {
            $h += 360;
        }

        return [
            'l' => $L,
            'c' => $c,
            'h' => $h,
        ];
    }

    /**
     * Apply sRGB gamma correction (linear ’ sRGB).
     */
    private static function gammaCorrection(float $value): float
    {
        if ($value <= 0.0031308) {
            return 12.92 * $value;
        }

        return 1.055 * pow($value, 1 / 2.4) - 0.055;
    }

    /**
     * Apply inverse sRGB gamma correction (sRGB ’ linear).
     */
    private static function inverseGammaCorrection(float $value): float
    {
        if ($value <= 0.04045) {
            return $value / 12.92;
        }

        return pow(($value + 0.055) / 1.055, 2.4);
    }
}
