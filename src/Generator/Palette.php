<?php

namespace SocialDept\ColorScales\Generator;

use SocialDept\ColorScales\ColorSpaces\Color;
use SocialDept\ColorScales\ColorSpaces\Hex;
use SocialDept\ColorScales\ColorSpaces\HSL;
use SocialDept\ColorScales\ColorSpaces\RGB;

/**
 * Represents a generated color palette with export capabilities.
 */
readonly class Palette
{
    /**
     * @param  array<int, Color>  $colors  Array of Color objects keyed by shade number
     */
    public function __construct(
        private array $colors
    ) {
    }

    /**
     * Export palette as hex color strings.
     *
     * @return array<int, string> ['50' => '#f2f1ff', '100' => '#e7e6ff', ...]
     */
    public function toHex(): array
    {
        $result = [];

        foreach ($this->colors as $shade => $color) {
            $result[(string) $shade] = $color->toHex();
        }

        return $result;
    }

    /**
     * Export palette as RGB strings.
     *
     * @return array<int, string> ['50' => 'rgb(242, 241, 255)', ...]
     */
    public function toRgb(): array
    {
        $result = [];

        foreach ($this->colors as $shade => $color) {
            $result[(string) $shade] = RGB::toString($color->toRgb());
        }

        return $result;
    }

    /**
     * Export palette as HSL strings.
     *
     * @return array<int, string> ['50' => 'hsl(245, 100%, 97%)', ...]
     */
    public function toHsl(): array
    {
        $result = [];

        foreach ($this->colors as $shade => $color) {
            $result[(string) $shade] = HSL::toString($color->toHsl());
        }

        return $result;
    }

    /**
     * Export palette as OKLCH strings.
     *
     * @return array<int, string> ['50' => 'oklch(0.975 0.02 275)', ...]
     */
    public function toOklch(): array
    {
        $result = [];

        foreach ($this->colors as $shade => $color) {
            $oklch = $color->toOklch();
            $result[(string) $shade] = sprintf(
                'oklch(%.3f %.3f %.1f)',
                $oklch['l'],
                $oklch['c'],
                $oklch['h']
            );
        }

        return $result;
    }

    /**
     * Export palette in Tailwind v3 config format (JavaScript object).
     * Uses OKLCH with alpha value placeholder for opacity support.
     *
     * @param  string  $name  Color scale name (e.g., 'primary', 'blue')
     * @param  string  $format  Color format: 'oklch', 'hex', 'rgb', 'hsl'
     * @return string JavaScript object for tailwind.config.js
     */
    public function toTailwindV3Config(string $name = 'primary', string $format = 'oklch'): string
    {
        $lines = ["'{$name}': {"];

        foreach ($this->colors as $shade => $color) {
            $value = match ($format) {
                'oklch' => $this->formatOklchWithAlpha($color),
                'hex' => $color->toHex(),
                'rgb' => RGB::toString($color->toRgb()),
                'hsl' => HSL::toString($color->toHsl()),
                default => $color->toHex(),
            };

            $lines[] = "  {$shade}: '{$value}',";
        }

        $lines[] = '}';

        return implode("\n", $lines);
    }

    /**
     * Export palette in Tailwind v4 config format (CSS @theme).
     * Uses CSS custom properties with color values.
     *
     * @param  string  $name  Color scale name (e.g., 'primary', 'blue')
     * @param  string  $format  Color format: 'oklch', 'hex', 'rgb', 'hsl'
     * @return string CSS @theme block
     */
    public function toTailwindV4Config(string $name = 'primary', string $format = 'oklch'): string
    {
        $lines = ['@theme {'];

        foreach ($this->colors as $shade => $color) {
            $value = match ($format) {
                'oklch' => $this->formatOklch($color),
                'hex' => $color->toHex(),
                'rgb' => $this->formatRgbForV4($color),
                'hsl' => $this->formatHslForV4($color),
                default => $this->formatOklch($color),
            };

            $lines[] = "  --color-{$name}-{$shade}: {$value};";
        }

        $lines[] = '}';

        return implode("\n", $lines);
    }

    /**
     * Export palette in Tailwind config format.
     * Alias for toTailwindV3Config() for backward compatibility.
     *
     * @param  string  $name  Color scale name (e.g., 'primary', 'blue')
     * @return string JavaScript object for tailwind.config.js
     */
    public function toTailwindConfig(string $name = 'primary'): string
    {
        return $this->toTailwindV3Config($name, 'hex');
    }

    /**
     * Format OKLCH color for Tailwind v4 (without alpha placeholder).
     */
    private function formatOklch(Color $color): string
    {
        $oklch = $color->toOklch();

        return sprintf(
            'oklch(%.3f %.3f %.2f)',
            $oklch['l'],
            $oklch['c'],
            $oklch['h']
        );
    }

    /**
     * Format OKLCH color with alpha value placeholder for Tailwind v3.
     */
    private function formatOklchWithAlpha(Color $color): string
    {
        $oklch = $color->toOklch();

        return sprintf(
            'oklch(%.3f %.3f %.2f / <alpha-value>)',
            $oklch['l'],
            $oklch['c'],
            $oklch['h']
        );
    }

    /**
     * Format RGB color for Tailwind v4 CSS.
     */
    private function formatRgbForV4(Color $color): string
    {
        $rgb = $color->toRgb();

        return sprintf('rgb(%d %d %d)', $rgb['r'], $rgb['g'], $rgb['b']);
    }

    /**
     * Format HSL color for Tailwind v4 CSS.
     */
    private function formatHslForV4(Color $color): string
    {
        $hsl = $color->toHsl();

        return sprintf('hsl(%.1f %.1f%% %.1f%%)', $hsl['h'], $hsl['s'], $hsl['l']);
    }

    /**
     * Export palette as array (alias for toHex).
     *
     * @return array<int, string>
     */
    public function toArray(): array
    {
        return $this->toHex();
    }

    /**
     * Get a specific shade from the palette.
     */
    public function getShade(int $shade): ?Color
    {
        return $this->colors[$shade] ?? null;
    }

    /**
     * Get all colors in the palette.
     *
     * @return array<int, Color>
     */
    public function getColors(): array
    {
        return $this->colors;
    }
}
