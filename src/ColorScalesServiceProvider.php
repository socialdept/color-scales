<?php

namespace SocialDept\ColorScales;

use Illuminate\Support\ServiceProvider;
use SocialDept\ColorScales\Generator\ColorScaleGenerator;

class ColorScalesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ColorScaleGenerator::class, function ($app) {
            return new ColorScaleGenerator();
        });
    }
}
