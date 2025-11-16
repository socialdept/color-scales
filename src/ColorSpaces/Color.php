<?php

namespace SocialDept\ColorScales\ColorSpaces;

/**
 * Immutable color value object.
 * Stores color internally as OKLCH for perceptually uniform manipulation.
 */
readonly class Color
{
    /**
     * @param float $l Lightness (0-1, where 0 is black, 1 is white)
     * @param float $c Chroma (0-0.4+, unbounded but typically 0-0.4)
     * @param float $h Hue (0-360 degrees)
     */
    public function __construct(
        public float $l,
        public float $c,
        public float $h,
    ) {
    }

    public static function fromHex(string $hex): self
    {
        return Hex::toColor($hex);
    }

    public static function fromRgb(int|float $r, int|float $g, int|float $b): self
    {
        return RGB::toColor($r, $g, $b);
    }

    public static function fromHsl(float $h, float $s, float $l): self
    {
        return HSL::toColor($h, $s, $l);
    }

    public static function fromOklch(float $l, float $c, float $h): self
    {
        return new self($l, $c, $h);
    }

    public static function fromHsluv(float $h, float $s, float $l): self
    {
        return HSLuv::toColor($h, $s, $l);
    }

    public function toHex(): string
    {
        return Hex::fromColor($this);
    }

    public function toRgb(): array
    {
        return RGB::fromColor($this);
    }

    public function toHsl(): array
    {
        return HSL::fromColor($this);
    }

    public function toOklch(): array
    {
        return [
            'l' => $this->l,
            'c' => $this->c,
            'h' => $this->h,
        ];
    }

    public function toHsluv(): array
    {
        return HSLuv::fromColor($this);
    }

    /**
     * Clamp this color to the sRGB gamut.
     * If the OKLCH color cannot be represented in sRGB, reduce chroma until it can.
     */
    public function clampToRgb(): self
    {
        $rgb = OKLCH::toRgb($this->l, $this->c, $this->h);

        // Check if color is already in gamut
        if ($rgb['r'] >= 0 && $rgb['r'] <= 255 &&
            $rgb['g'] >= 0 && $rgb['g'] <= 255 &&
            $rgb['b'] >= 0 && $rgb['b'] <= 255) {
            return $this;
        }

        // Binary search for maximum chroma that stays in gamut
        $minC = 0;
        $maxC = $this->c;
        $epsilon = 0.0001;

        while ($maxC - $minC > $epsilon) {
            $testC = ($minC + $maxC) / 2;
            $testRgb = OKLCH::toRgb($this->l, $testC, $this->h);

            if ($testRgb['r'] >= 0 && $testRgb['r'] <= 255 &&
                $testRgb['g'] >= 0 && $testRgb['g'] <= 255 &&
                $testRgb['b'] >= 0 && $testRgb['b'] <= 255) {
                $minC = $testC;
            } else {
                $maxC = $testC;
            }
        }

        return new self($this->l, $minC, $this->h);
    }

    /**
     * Create a new color with modified lightness.
     */
    public function withLightness(float $l): self
    {
        return new self($l, $this->c, $this->h);
    }

    /**
     * Create a new color with modified chroma.
     */
    public function withChroma(float $c): self
    {
        return new self($this->l, $c, $this->h);
    }

    /**
     * Create a new color with modified hue.
     */
    public function withHue(float $h): self
    {
        return new self($this->l, $this->c, $h);
    }
}
