# Installation

Part of the [color-scales docs](../introduction.md#documentation). Installing the package and what it registers in Laravel.

## Installing

```bash
composer require socialdept/color-scales
```

## Configuration

There is no config file to publish. Laravel discovers `ColorScalesServiceProvider`, which binds `ColorScaleGenerator` as a singleton, so it can be injected or resolved from the container:

```php
use SocialDept\ColorScales\Generator\ColorScaleGenerator;

$generator = app(ColorScaleGenerator::class);
```
