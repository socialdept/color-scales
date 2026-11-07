# Formats and shades

Part of the [color-scales docs](../introduction.md#documentation). Hex, RGB, HSL and OKLCH arrays, and reading single shades as `Color` objects.

## Formats

Each method returns an array of strings keyed by shade, from 50 to 950. The values below are for `#511ef3`:

```php
$palette = $generator->generate('#511ef3');

$palette->toHex();
// [50 => '#f0effe', 100 => '#e5e3fe', ...]

$palette->toRgb();
// [50 => 'rgb(240, 239, 254)', ...]

$palette->toHsl();
// [50 => 'hsl(244.0, 88.2%, 96.7%)', ...]

$palette->toOklch();
// [50 => 'oklch(0.957 0.020 289.1)', ...]

// An alias for toHex()
$palette->toArray();
```

## Single shades

`getShade()` returns one shade as a `Color`, or `null` when the palette has no such shade. `getColors()` returns all of them, keyed by shade:

```php
$shade = $palette->getShade(700);

$shade->toHex();   // '#511ef3'
$shade->toRgb();   // ['r' => 81, 'g' => 30, 'b' => 243]
$shade->toOklch(); // ['l' => 0.484..., 'c' => 0.276..., 'h' => 279.38...]

$palette->getColors();
// [50 => Color, 100 => Color, ..., 950 => Color]
```

A `Color` also has `toHsl()` and `toHsluv()`, which return arrays keyed `h`, `s` and `l`.
