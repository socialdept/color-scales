<?php

namespace SocialDept\ColorScales\ColorSpaces;

class HSL
{
    public static function toColor(float $h, float $s, float $l): Color
    {
        // Convert HSL to RGB first
        $rgb = self::hslToRgb($h, $s, $l);

        return RGB::toColor($rgb['r'], $rgb['g'], $rgb['b']);
    }

    public static function fromColor(Color $color): array
    {
        $rgb = RGB::fromColor($color);

        return self::rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
    }

    public static function toString(array $hsl): string
    {
        return sprintf(
            'hsl(%.1f, %.1f%%, %.1f%%)',
            $hsl['h'],
            $hsl['s'],
            $hsl['l']
        );
    }

    /**
     * Convert HSL to RGB (0-255).
     */
    private static function hslToRgb(float $h, float $s, float $l): array
    {
        $h = $h / 360; // Normalize to 0-1
        $s = $s / 100; // Normalize to 0-1
        $l = $l / 100; // Normalize to 0-1

        if ($s == 0) {
            $r = $g = $b = $l * 255;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = self::hueToRgb($p, $q, $h + 1 / 3) * 255;
            $g = self::hueToRgb($p, $q, $h) * 255;
            $b = self::hueToRgb($p, $q, $h - 1 / 3) * 255;
        }

        return [
            'r' => (int) round($r),
            'g' => (int) round($g),
            'b' => (int) round($b),
        ];
    }

    /**
     * Convert RGB (0-255) to HSL.
     */
    private static function rgbToHsl(int $r, int $g, int $b): array
    {
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            $h = match ($max) {
                $r => (($g - $b) / $d + ($g < $b ? 6 : 0)),
                $g => (($b - $r) / $d + 2),
                $b => (($r - $g) / $d + 4),
            };

            $h = $h / 6;
        }

        return [
            'h' => $h * 360,
            's' => $s * 100,
            'l' => $l * 100,
        ];
    }

    private static function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) {
            $t += 1;
        }
        if ($t > 1) {
            $t -= 1;
        }
        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1 / 2) {
            return $q;
        }
        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }

        return $p;
    }
}
