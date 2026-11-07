# Introduction

**Color Scales** generates Tailwind-compatible color palettes from a single input color. It builds 11 shades, from 50 to 950, following the lightness and saturation model of [tints.dev](https://tints.dev):

- **Shade detection:** works out which shade the input color belongs at, and keeps it there unchanged.
- **Two modes:** `perceived` builds the scale in HSLuv, `linear` builds it in HSL.
- **Input formats:** hex, `rgb()`, `hsl()` and `oklch()` strings.
- **Export formats:** hex, RGB, HSL and OKLCH strings, a Tailwind v4 `@theme` block or a Tailwind v3 config object.
- **Tuning:** shift the hue or saturation across the scale, set the lightness bounds, or pin the input color to a shade.
- **Gamut clamping:** a shade that falls outside sRGB has its chroma reduced until it fits.

## A first look

Pass any supported color string, and the palette comes back with the input color at the shade it fits best. `#d8b965` lands at 300:

```php
use SocialDept\ColorScales\ColorScale;

$palette = ColorScale::generate('#d8b965');

$palette->toHex();
// [
//   50 => '#fcf3e1',
//   100 => '#f9e6c0',
//   200 => '#f1ce71',
//   300 => '#d8b965',
//   400 => '#b69b54',
//   ...
// ]
```

The same palette can be written straight into a Tailwind config:

```php
echo $palette->toTailwindConfig('gold');
// 'gold': {
//   50: '#fcf3e1',
//   100: '#f9e6c0',
//   ...
// }
```

## Documentation

Start with [Installation](getting-started/installation.md), then [Generating palettes](getting-started/generating.md) for the input formats. The docs are grouped by area, and each group has an index of its own:

- **[Getting started](getting-started/README.md)**: [Installation](getting-started/installation.md), [Generating palettes](getting-started/generating.md)
- **[Shaping palettes](palettes/README.md)**: [Modes and shade detection](palettes/modes.md), [Options](palettes/options.md)
- **[Exporting](export/README.md)**: [Formats and shades](export/formats.md), [Tailwind config](export/tailwind.md)
- **[Examples](examples.md)**: Theme files, per-user brand palettes and palette variants.
