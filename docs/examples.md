# Examples

Part of the [color-scales docs](introduction.md#documentation). Theme files, per-user brand palettes and palette variants.

## A Tailwind theme

Generate one palette per role and print them as Tailwind config:

```php
use SocialDept\ColorScales\Generator\ColorScaleGenerator;

$generator = new ColorScaleGenerator();

$colors = [
    'primary' => $generator->generate('#3b82f6'),
    'success' => $generator->generate('#10b981'),
    'warning' => $generator->generate('#f59e0b'),
    'danger' => $generator->generate('#ef4444'),
];

// Tailwind v4 (CSS @theme)
foreach ($colors as $name => $palette) {
    echo $palette->toTailwindV4Config($name) . "\n\n";
}

// Tailwind v3 (OKLCH with alpha support)
foreach ($colors as $name => $palette) {
    echo $palette->toTailwindV3Config($name, 'oklch') . "\n\n";
}
```

## A brand color per user

Build a palette from a color the user picked and cache the hex values:

```php
$palette = $generator->generate($request->input('brand_color'));

cache()->put("brand_palette_{$userId}", $palette->toHex(), now()->addDay());
```

An invalid color string throws an `InvalidArgumentException`, so validate the input first.

## Variants of one color

Use the saturation shift to derive brighter and quieter versions of the same base, then write one out as CSS variables:

```php
$baseColor = '#511ef3';

$palettes = [
    'default' => $generator->generate($baseColor),
    'vibrant' => $generator->generate($baseColor, ['s' => 10]),
    'muted' => $generator->generate($baseColor, ['s' => -10]),
];

foreach ($palettes['default']->toHex() as $shade => $hex) {
    echo "--color-{$shade}: {$hex};\n";
}
```
