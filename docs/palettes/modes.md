# Modes and shade detection

Part of the [color-scales docs](../introduction.md#documentation). Perceived and linear mode, and how the input color's shade is chosen.

## Modes

`perceived` is the default. `linear` is the alternative:

```php
// Perceived mode (HSLuv), the default
$palette = $generator->generate('#d8b965', ['mode' => 'perceived']);
$palette = $generator->generatePerceived('#d8b965');

// Linear mode (HSL)
$palette = $generator->generate('#d8b965', ['mode' => 'linear']);
$palette = $generator->generateLinear('#d8b965');
```

**Perceived mode** picks the input color's shade by its relative luminance and builds the other shades in HSLuv, so steps of the same size look about the same distance apart. **Linear mode** uses HSL lightness for both, which gives a simpler, mathematical spread.

## Shade detection

The input color is not always shade 500. The generator compares it with a baseline palette and places it at the closest shade:

```php
$generator->generate('#d8b965')->toHex()[300]; // '#d8b965'
$generator->generate('#3b82f6')->toHex()[500]; // '#3b82f6'
$generator->generate('#511ef3')->toHex()[700]; // '#511ef3'
```

In perceived mode the baseline is the relative luminance of tints.dev's blue palette, with `#1E70F6` at 500. In linear mode it is a fixed list of HSL lightness values, 94 at 50 down to 15 at 950.

The input color is kept exactly at its shade as long as the hue and saturation shifts are both 0. The other shades are interpolated in lightness between white, the input color and black, then clamped to the sRGB gamut. Use the `valueStop` [option](options.md#pinning-the-input-color) to choose the shade yourself.

> [!WARNING]
> Colors close to white or black, such as `#ffffff`, `#fefefe` or `#000000`, are matched to the white or black end of the baseline rather than to a shade, so they do not appear in the palette and the result is a mid gray scale.
