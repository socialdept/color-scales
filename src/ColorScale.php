<?php

namespace SocialDept\ColorScales;

use SocialDept\ColorScales\Generator\ColorScaleGenerator;
use SocialDept\ColorScales\Generator\Palette;

/**
 * Color Scale - Main facade for generating Tailwind color palettes.
 *
 * @example
 * ```php
 * use SocialDept\ColorScales\ColorScale;
 *
 * // Generate palette from hex color
 * $palette = ColorScale::generate('#511ef3');
 *
 * // With options (perceived mode - default)
 * $palette = ColorScale::generate('#511ef3', ['mode' => 'perceived']);
 *
 * // Linear mode
 * $palette = ColorScale::generate('#511ef3', ['mode' => 'linear']);
 *
 * // Different input formats
 * $palette = ColorScale::generate('rgb(81, 30, 243)');
 * $palette = ColorScale::generate('hsl(254, 90%, 54%)');
 * $palette = ColorScale::generate('oklch(0.48 0.29 275)');
 *
 * // Export in different formats
 * $hex = $palette->toHex();       // ['50' => '#f2f1ff', ...]
 * $rgb = $palette->toRgb();       // ['50' => 'rgb(242, 241, 255)', ...]
 * $hsl = $palette->toHsl();       // ['50' => 'hsl(245, 100%, 97%)', ...]
 * $oklch = $palette->toOklch();   // ['50' => 'oklch(0.975 0.02 275)', ...]
 * $configV4 = $palette->toTailwindV4Config('primary');
 * $configV3 = $palette->toTailwindV3Config('primary', 'oklch');
 * ```
 */
class ColorScale
{
    private static ?ColorScaleGenerator $generator = null;

    /**
     * Generate a Tailwind color palette from any input color.
     *
     * @param  string  $color  Input color in hex, rgb, hsl, or oklch format
     * @param  array  $options  Optional parameters: h, s, lMin, lMax, valueStop
     * @return Palette Generated palette with export methods
     */
    public static function generate(string $color, array $options = []): Palette
    {
        return self::getGenerator()->generate($color, $options);
    }

    /**
     * Get or create the generator instance.
     */
    private static function getGenerator(): ColorScaleGenerator
    {
        if (self::$generator === null) {
            self::$generator = new ColorScaleGenerator();
        }

        return self::$generator;
    }
}
