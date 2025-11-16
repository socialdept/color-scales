<?php

namespace SocialDept\ColorScales\Algorithms;

use SocialDept\ColorScales\ColorSpaces\Color;
use SocialDept\ColorScales\ColorSpaces\HSL;
use SocialDept\ColorScales\ColorSpaces\HSLuv;

class TailwindCharacteristics
{
    private const SHADES = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];

    // All stops including calculation anchors (like tints.dev)
    private const ALL_STOPS = [0, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950, 1000];

    // Baseline palette for auto-detection (blue #1E70F6 at stop 500 in perceived mode)
    // Luminance values (0-100) from tints.dev's baseline palette hex colors
    private const BASELINE_PERCEIVED = [
        0 => 100.0,      // #FFFFFF
        50 => 91.59,     // #F0F6FE
        100 => 83.32,    // #E2ECFE
        200 => 66.71,    // #BFD7FC
        300 => 50.47,    // #98BEFB
        400 => 34.52,    // #679FF9
        500 => 18.52,    // #1E70F6
        600 => 15.09,    // #0A62F0
        700 => 10.85,    // #0854CE
        800 => 6.97,     // #0744A7
        900 => 4.02,     // #05347F
        950 => 1.99,     // #042458
        1000 => 0.0,     // #000000
    ];

    // Baseline for linear mode (HSL lightness values)
    private const BASELINE_LINEAR = [
        0 => 100.0,
        50 => 94.0,
        100 => 89.0,
        200 => 78.0,
        300 => 67.0,
        400 => 56.0,
        500 => 48.0,
        600 => 39.0,
        700 => 32.0,
        800 => 25.0,
        900 => 19.0,
        950 => 15.0,
        1000 => 0.0,
    ];

    public function __construct(
        private float $h = 0,        // Hue shift parameter
        private float $s = 0,        // Saturation shift parameter
        private float $lMin = 0,     // Darkest lightness (stop 1000)
        private float $lMax = 100,   // Lightest lightness (stop 0)
        private int $valueStop = 500, // Which stop the input color maps to
        private string $mode = 'perceived' // 'perceived' (HSLuv) or 'linear' (HSL)
    ) {
    }

    public function generate(Color $inputColor): array
    {
        // Auto-detect valueStop if not explicitly set
        $valueStop = $this->valueStop;
        if ($valueStop === 500) {
            $valueStop = $this->determineValueStop($inputColor);
        }

        // Find index of valueStop in ALL_STOPS
        $valueStopIndex = array_search($valueStop, self::ALL_STOPS);

        // Get base color values in appropriate mode
        if ($this->mode === 'linear') {
            $hsl = $inputColor->toHsl();
            $baseH = $hsl['h'];
            $baseS = $hsl['s'];
            $baseL = $hsl['l'];
            $inputLightness = $baseL;
        } else {
            $hsluv = $inputColor->toHsluv();
            $baseH = $hsluv['h'];
            $baseS = $hsluv['s'];
            $baseL = $hsluv['l'];
            $inputLightness = $baseL;
        }

        // Handle grayscale (NaN hue)
        $baseH = is_nan($baseH) ? 0 : $baseH;

        // Calculate scales
        $hueScale = $this->calculateHueScale($valueStopIndex);
        $saturationScale = $this->calculateSaturationScale($valueStopIndex);
        $lightnessScale = $this->calculateLightnessScale($valueStopIndex, $inputLightness);

        $palette = [];

        foreach (self::SHADES as $shade) {
            if ($shade === $valueStop) {
                // Preserve the exact input color at valueStop
                $palette[$shade] = $inputColor;

                continue;
            }

            $stopIndex = array_search($shade, self::ALL_STOPS);

            // Get tweaks for this stop
            $hTweak = $hueScale[$shade]['tweak'];
            $sTweak = $saturationScale[$shade]['tweak'];
            $lTweak = $lightnessScale[$shade]['tweak'];

            // Apply tweaks based on mode
            if ($this->mode === 'linear') {
                $newH = fmod($baseH + $hTweak + 360, 360);
                $newS = max(0, min(100, $baseS + $sTweak));
                $newL = max(0, min(100, $lTweak));

                $palette[$shade] = Color::fromHsl($newH, $newS, $newL)->clampToRgb();
            } else {
                $newH = fmod($baseH + $hTweak + 360, 360);
                $newS = max(0, min(100, $baseS + $sTweak));
                $newL = max(0, min(100, $lTweak));

                $palette[$shade] = Color::fromHsluv($newH, $newS, $newL)->clampToRgb();
            }
        }

        return $palette;
    }

    /**
     * Determine which shade the input color should map to based on lightness.
     * Uses baseline palette comparison like tints.dev.
     */
    private function determineValueStop(Color $inputColor): int
    {
        // Get lightness value in appropriate mode
        if ($this->mode === 'linear') {
            $hsl = $inputColor->toHsl();
            $inputValue = $hsl['l'];
        } else {
            // In perceived mode, use relative luminance (like tints.dev)
            $inputValue = $this->calculateLuminance($inputColor) * 100;
        }

        // Choose baseline
        $baseline = $this->mode === 'linear' ? self::BASELINE_LINEAR : self::BASELINE_PERCEIVED;

        // Find closest match
        $closestStop = 500;
        $smallestDiff = PHP_FLOAT_MAX;

        foreach ($baseline as $stop => $value) {
            $diff = abs($value - $inputValue);
            if ($diff < $smallestDiff) {
                $smallestDiff = $diff;
                $closestStop = $stop;
            }
        }

        return $closestStop;
    }

    /**
     * Calculate relative luminance (0-1) from sRGB color.
     * Formula: 0.2126 * R + 0.7152 * G + 0.0722 * B (where R,G,B are linearized)
     */
    private function calculateLuminance(Color $color): float
    {
        $rgb = $color->toRgb();

        // Linearize sRGB values
        $linearize = function ($c) {
            $c = $c / 255.0;

            return $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
        };

        $r = $linearize($rgb['r']);
        $g = $linearize($rgb['g']);
        $b = $linearize($rgb['b']);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Calculate hue scale for all stops.
     * Formula: |stopIndex - valueStopIndex| × h
     */
    private function calculateHueScale(int $valueStopIndex): array
    {
        $scale = [];
        foreach (self::ALL_STOPS as $stop) {
            $stopIndex = array_search($stop, self::ALL_STOPS);
            $diff = abs($stopIndex - $valueStopIndex);
            $tweak = $this->h !== 0 ? $diff * $this->h : 0;
            $scale[$stop] = ['stop' => $stop, 'tweak' => $tweak];
        }

        return $scale;
    }

    /**
     * Calculate saturation scale for all stops.
     * Formula: min(100, (diff + 1) × s × (1 + diff / 10))
     */
    private function calculateSaturationScale(int $valueStopIndex): array
    {
        $scale = [];
        foreach (self::ALL_STOPS as $stop) {
            $stopIndex = array_search($stop, self::ALL_STOPS);
            $diff = abs($stopIndex - $valueStopIndex);
            $tweak = $this->s !== 0 ? min(100, round(($diff + 1) * $this->s * (1 + $diff / 10))) : 0;
            $scale[$stop] = ['stop' => $stop, 'tweak' => $tweak];
        }

        return $scale;
    }

    /**
     * Calculate lightness scale using three-point piecewise linear interpolation.
     * Based on tints.dev algorithm.
     *
     * Anchors:
     * - Stop 0: lMax (lightest, default 100%)
     * - valueStop: input color lightness (preserved exactly)
     * - Stop 1000: lMin (darkest, default 0%)
     */
    private function calculateLightnessScale(int $valueStopIndex, float $inputLightness): array
    {
        // Three anchor points for piecewise linear interpolation
        $valueStop = self::ALL_STOPS[$valueStopIndex];

        $anchors = [
            ['stop' => 0, 'index' => 0, 'lightness' => $this->lMax],
            ['stop' => $valueStop, 'index' => $valueStopIndex, 'lightness' => $inputLightness],
            ['stop' => 1000, 'index' => count(self::ALL_STOPS) - 1, 'lightness' => $this->lMin],
        ];

        $scale = [];

        foreach (self::ALL_STOPS as $stop) {
            $stopIndex = array_search($stop, self::ALL_STOPS);

            // If this is an anchor point, use anchor value
            $isAnchor = false;
            foreach ($anchors as $anchor) {
                if ($anchor['stop'] === $stop) {
                    $scale[$stop] = ['stop' => $stop, 'tweak' => round($anchor['lightness'])];
                    $isAnchor = true;

                    break;
                }
            }

            if ($isAnchor) {
                continue;
            }

            // Find which two anchors this stop is between
            $leftAnchor = null;
            $rightAnchor = null;

            for ($i = 0; $i < count($anchors) - 1; $i++) {
                if ($stopIndex >= $anchors[$i]['index'] && $stopIndex <= $anchors[$i + 1]['index']) {
                    $leftAnchor = $anchors[$i];
                    $rightAnchor = $anchors[$i + 1];

                    break;
                }
            }

            // Linear interpolation between anchors using actual stop values
            $range = $rightAnchor['stop'] - $leftAnchor['stop'];
            $position = $stop - $leftAnchor['stop'];
            $ratio = $range > 0 ? $position / $range : 0;

            $lightness = $leftAnchor['lightness'] + ($rightAnchor['lightness'] - $leftAnchor['lightness']) * $ratio;
            $scale[$stop] = ['stop' => $stop, 'tweak' => round($lightness)];
        }

        return $scale;
    }
}
