<?php namespace App\Providers;

use Elegant\Sanitizer\Laravel\Factory as Sanitizer;
use Illuminate\Support\ServiceProvider;
use App\Filters\Strip as Strip;
use App\Filters\Array2 as Array2;
use App\Filters\StripDangerTags as StripDangerTags;


class SanitizerServiceProvider extends ServiceProvider
{
    private $sanitizers = [
        'strip' => Strip::class,
        'array' => Array2::class,
        'strip_danger_tags' => StripDangerTags::class,
    ];
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        app()->afterResolving(Sanitizer::class, function($s, $app) {
            foreach($this->sanitizers as $key => $value)
                $s->extend($key, $value);
        });
    }

}
