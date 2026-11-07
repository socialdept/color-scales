# Tailwind config

Part of the [color-scales docs](../introduction.md#documentation). A Tailwind v4 `@theme` block or a Tailwind v3 config object, in any of the four formats.

Both methods take the color's name, which defaults to `primary`, and a format: `oklch` (the default), `hex`, `rgb` or `hsl`. The values below are for `#511ef3`.

## Tailwind v4

`toTailwindV4Config()` returns a CSS `@theme` block of custom properties:

```php
$palette->toTailwindV4Config('primary');
// @theme {
//   --color-primary-50: oklch(0.957 0.020 289.08);
//   --color-primary-100: oklch(0.926 0.036 289.07);
//   ...
// }

$palette->toTailwindV4Config('primary', 'hex');
// --color-primary-50: #f0effe;

$palette->toTailwindV4Config('primary', 'rgb');
// --color-primary-50: rgb(240 239 254);

$palette->toTailwindV4Config('primary', 'hsl');
// --color-primary-50: hsl(244.0 88.2% 96.7%);
```

## Tailwind v3

`toTailwindV3Config()` returns an entry for the `colors` object in `tailwind.config.js`. In OKLCH it adds the `<alpha-value>` placeholder, so opacity modifiers keep working:

```php
$palette->toTailwindV3Config('primary');
// 'primary': {
//   50: 'oklch(0.957 0.020 289.08 / <alpha-value>)',
//   100: 'oklch(0.926 0.036 289.07 / <alpha-value>)',
//   ...
// }

$palette->toTailwindV3Config('primary', 'hex');
// 'primary': {
//   50: '#f0effe',
//   100: '#e5e3fe',
//   ...
// }
```

The `rgb` and `hsl` formats use the same strings as `toRgb()` and `toHsl()`.

`toTailwindConfig($name)` is kept for backward compatibility and returns the same as `toTailwindV3Config($name, 'hex')`.
