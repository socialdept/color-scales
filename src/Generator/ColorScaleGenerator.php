<?php

namespace SocialDept\ColorScales\Generator;

use InvalidArgumentException;
use SocialDept\ColorScales\Algorithms\TailwindCharacteristics;
use SocialDept\ColorScales\ColorSpaces\Color;

class ColorScaleGenerator
{
    /**
     * Generate a color palette from an input color.
     *
     * @param  string  $color  Input color (hex, rgb, hsl, or oklch format)
     * @param  array  $options  Optional parameters: h, s, lMin, lMax, valueStop, mode
     *                         - h: Hue shift (default: 0)
     *                         - s: Saturation shift (default: 0)
     *                         - lMin: Minimum lightness (default: 0)
     *                         - lMax: Maximum lightness (default: 100)
     *                         - valueStop: Which shade the input color maps to (default: 500, auto-detected)
     *                         - mode: 'perceived' (HSLuv) or 'linear' (HSL) (default: 'perceived')
     * @return Palette Generated palette with export methods
     */
    public function generate(string $color, array $options = []): Palette
    {
        $colorObject = $this->parseColor($color);
        $generator = $this->getAlgorithm($options);

        $colors = $generator->generate($colorObject);

        return new Palette($colors);
    }

    /**
     * Generate a palette using perceived mode (HSLuv - perceptually uniform).
     *
     * @param  string  $color  Input color
     * @param  array  $options  Optional parameters (h, s, lMin, lMax, valueStop)
     * @return Palette Generated palette
     */
    public function generatePerceived(string $color, array $options = []): Palette
    {
        return $this->generate($color, array_merge($options, ['mode' => 'perceived']));
    }

    /**
     * Generate a palette using linear mode (HSL - mathematically simple).
     *
     * @param  string  $color  Input color
     * @param  array  $options  Optional parameters (h, s, lMin, lMax, valueStop)
     * @return Palette Generated palette
     */
    public function generateLinear(string $color, array $options = []): Palette
    {
        return $this->generate($color, array_merge($options, ['mode' => 'linear']));
    }

    /**
     * Parse input color string to Color object.
     */
    private function parseColor(string $input): Color
    {
        $input = trim($input);

        // Hex format: #RRGGBB or RRGGBB or #RGB
        if (preg_match('/^#?[0-9a-fA-F]{3,6}$/', $input)) {
            return Color::fromHex($input);
        }

        // RGB format: rgb(r, g, b) or rgb(r g b)
        if (preg_match('/^rgb\(\s*(\d+)[,\s]+(\d+)[,\s]+(\d+)\s*\)$/i', $input, $matches)) {
            return Color::fromRgb((int) $matches[1], (int) $matches[2], (int) $matches[3]);
        }

        // HSL format: hsl(h, s%, l%) or hsl(h s% l%)
        if (preg_match('/^hsl\(\s*(\d+\.?\d*)[,\s]+(\d+\.?\d*)%?[,\s]+(\d+\.?\d*)%?\s*\)$/i', $input, $matches)) {
            return Color::fromHsl((float) $matches[1], (float) $matches[2], (float) $matches[3]);
        }

        // OKLCH format: oklch(l c h) or oklch(l, c, h)
        if (preg_match('/^oklch\(\s*(\d+\.?\d*)[,\s]+(\d+\.?\d*)[,\s]+(\d+\.?\d*)\s*\)$/i', $input, $matches)) {
            return Color::fromOklch((float) $matches[1], (float) $matches[2], (float) $matches[3]);
        }

        throw new InvalidArgumentException("Invalid color format: {$input}. Supported formats: hex (#RRGGBB), rgb(r, g, b), hsl(h, s%, l%), oklch(l, c, h)");
    }

    /**
     * Get algorithm instance with options.
     */
    private function getAlgorithm(array $options = []): TailwindCharacteristics
    {
        if (empty($options)) {
            return new TailwindCharacteristics();
        }

        return new TailwindCharacteristics(
            h: $options['h'] ?? 0,
            s: $options['s'] ?? 0,
            lMin: $options['lMin'] ?? 0,
            lMax: $options['lMax'] ?? 100,
            valueStop: $options['valueStop'] ?? 500,
            mode: $options['mode'] ?? 'perceived'
        );
    }
}
