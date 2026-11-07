# Options

Part of the [color-scales docs](../introduction.md#documentation). Hue and saturation shifts, lightness bounds, and pinning the input color to a shade.

Every generate method takes an options array as its second argument. These are the keys and their defaults:

```php
$palette = $generator->generate('#511ef3', [
    'mode' => 'perceived', // 'perceived' or 'linear'
    'h' => 0,              // hue shift per shade, in degrees
    's' => 0,              // saturation shift, in percent of the input's saturation
    'lMin' => 0,           // lightness of the black end of the scale
    'lMax' => 100,         // lightness of the white end of the scale
    'valueStop' => 500,    // the input color's shade, 500 means detect it
]);
```

## Hue shift

`h` rotates the hue of each shade by `h` degrees for every step it sits away from the input color's shade. A positive value turns the light and dark ends one way around the color wheel:

```php
$palette = $generator->generate('#511ef3', ['h' => 5]);
```

## Saturation shift

`s` changes the saturation of every shade by the same share of the input's saturation, so `10` makes each shade 10 percent more saturated and `-10` makes it 10 percent less:

```php
$vibrant = $generator->generate('#511ef3', ['s' => 10]);
$muted = $generator->generate('#511ef3', ['s' => -10]);
```

When `h` or `s` is anything other than 0, the input color's own shade is generated like the rest, so it no longer matches the input exactly.

## Lightness bounds

The scale is interpolated between a white end above 50 and a black end below 950. `lMax` sets the lightness of the white end and `lMin` the black end, from 0 to 100, in HSLuv lightness for perceived mode or HSL lightness for linear mode. Narrowing them pulls 50 down and 950 up:

```php
$palette = $generator->generate('#511ef3', ['lMin' => 10, 'lMax' => 95]);
```

## Pinning the input color

`valueStop` places the input color at a shade of your choosing instead of the detected one:

```php
$palette = $generator->generate('#d8b965', ['valueStop' => 600]);
```

> [!NOTE]
> 500 is the default and means detect the shade, so `'valueStop' => 500` cannot pin a color to 500. Use one of the other shades: 50, 100, 200, 300, 400, 600, 700, 800, 900 or 950.
