<?php

namespace SocialDept\ColorScales\Tests\Feature;

use SocialDept\ColorScales\ColorScale;
use SocialDept\ColorScales\Tests\TestCase;

class PaletteGenerationTest extends TestCase
{
    public function test_supports_different_input_formats(): void
    {
        $paletteHex = ColorScale::generate('#511ef3');
        $paletteRgb = ColorScale::generate('rgb(81, 30, 243)');
        $paletteHsl = ColorScale::generate('hsl(254, 88%, 54%)');

        // All should generate palettes with the same structure
        $hexResult = $paletteHex->toHex();
        $rgbResult = $paletteRgb->toHex();
        $hslResult = $paletteHsl->toHex();

        $this->assertIsArray($hexResult);
        $this->assertIsArray($rgbResult);
        $this->assertIsArray($hslResult);
        $this->assertCount(11, $hexResult);
        $this->assertCount(11, $rgbResult);
        $this->assertCount(11, $hslResult);
    }

    public function test_can_export_to_different_formats(): void
    {
        $palette = ColorScale::generate('#511ef3');

        $hex = $palette->toHex();
        $rgb = $palette->toRgb();
        $hsl = $palette->toHsl();
        $oklch = $palette->toOklch();
        $array = $palette->toArray();
        $tailwind = $palette->toTailwindConfig('primary');

        $this->assertIsArray($hex);
        $this->assertCount(11, $hex);
        $this->assertIsArray($rgb);
        $this->assertCount(11, $rgb);
        $this->assertIsArray($hsl);
        $this->assertCount(11, $hsl);
        $this->assertIsArray($oklch);
        $this->assertCount(11, $oklch);
        $this->assertSame($hex, $array);
        $this->assertStringContainsString("'primary'", $tailwind);
        $this->assertStringContainsString('50:', $tailwind);
        $this->assertStringContainsString('950:', $tailwind);
    }

    public function test_generates_palettes_with_monotonically_decreasing_lightness(): void
    {
        $palette = ColorScale::generate('#511ef3');
        $colors = $palette->getColors();

        $shades = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];
        $previousLightness = 1.0;

        foreach ($shades as $shade) {
            $color = $colors[$shade];
            $oklch = $color->toOklch();

            $this->assertLessThanOrEqual($previousLightness, $oklch['l']);
            $previousLightness = $oklch['l'];
        }
    }

    public function test_ensures_all_colors_are_valid_rgb(): void
    {
        $palette = ColorScale::generate('#511ef3');
        $colors = $palette->getColors();

        foreach ($colors as $shade => $color) {
            $rgb = $color->toRgb();

            $this->assertGreaterThanOrEqual(0, $rgb['r']);
            $this->assertLessThanOrEqual(255, $rgb['r']);
            $this->assertGreaterThanOrEqual(0, $rgb['g']);
            $this->assertLessThanOrEqual(255, $rgb['g']);
            $this->assertGreaterThanOrEqual(0, $rgb['b']);
            $this->assertLessThanOrEqual(255, $rgb['b']);
        }
    }
}
