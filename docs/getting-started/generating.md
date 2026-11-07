# Generating palettes

Part of the [color-scales docs](../introduction.md#documentation). The two ways to call the generator and the color strings it accepts.

## Calling the generator

`ColorScale::generate()` is a static shortcut that keeps one `ColorScaleGenerator` for you:

```php
use SocialDept\ColorScales\ColorScale;

$palette = ColorScale::generate('#511ef3');
$palette = ColorScale::generate('#511ef3', ['mode' => 'linear']);
```

`ColorScaleGenerator` can also be created directly or resolved from the container. It adds `generatePerceived()` and `generateLinear()`, which set the [mode](../palettes/modes.md) for you:

```php
use SocialDept\ColorScales\Generator\ColorScaleGenerator;

$generator = new ColorScaleGenerator();

$palette = $generator->generate('#511ef3');
$palette = $generator->generatePerceived('#511ef3');
$palette = $generator->generateLinear('#511ef3');
```

Each method takes the color string and an optional array of [options](../palettes/options.md), and returns a `Palette`. See [Exporting](../export/README.md) for what a palette gives back.

## Input formats

```php
// Hex, with or without the #, in 3 or 6 digits
$palette = $generator->generate('#511ef3');
$palette = $generator->generate('511ef3');
$palette = $generator->generate('#abc');

// RGB, with commas or spaces
$palette = $generator->generate('rgb(81, 30, 243)');
$palette = $generator->generate('rgb(81 30 243)');

// HSL, hue in degrees, saturation and lightness in percent
$palette = $generator->generate('hsl(254, 88%, 54%)');

// OKLCH, lightness from 0 to 1, with spaces or commas
$palette = $generator->generate('oklch(0.48 0.29 275)');
```

Leading and trailing whitespace is ignored. Alpha channels, 8-digit hex, OKLCH lightness written as a percentage and named colors are not accepted. Anything the generator cannot parse throws an `InvalidArgumentException`.
