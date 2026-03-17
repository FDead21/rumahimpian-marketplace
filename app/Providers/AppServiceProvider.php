<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View; 
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Observers\PropertyMediaObserver;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Illuminate\Auth\Events\Verified; 
use App\Listeners\AssignBlueBadge;  
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Event::listen(
            Verified::class,
            AssignBlueBadge::class,
        );

        if (! $this->app->runningInConsole() && Schema::hasTable('settings')) {
            $settings = \Cache::rememberForever('site_settings', function () {
                return Setting::all()->pluck('value', 'key')->toArray();
            });

            View::share('settings', $settings);
        }

        View::composer('*', function ($view) {
            if (Schema::hasTable('settings')) {
                $eoSettings = Cache::remember('eo_settings', 3600, function () {
                    $raw = Setting::where('key', 'like', 'eo_%')
                        ->pluck('value', 'key')
                        ->toArray();

                    foreach (['eo_hero_slides'] as $field) {
                        if (!empty($raw[$field])) {
                            $decoded = json_decode($raw[$field], true);
                            if (is_array($decoded)) {
                                $raw[$field] = $decoded;
                            }
                        }
                    }

                    return $raw;
                });

                $view->with('eoSettings', $eoSettings);
            }
        });

        Blade::directive('currency', function ($expression) {
            return "<?php
                \$num = $expression;
                if(\$num >= 1000000000) {
                    echo 'Rp ' . number_format(\$num / 1000000000, 1, ',', '.') . ' Miliar';
                } elseif(\$num >= 1000000) {
                    echo 'Rp ' . number_format(\$num / 1000000, 1, ',', '.') . ' Juta';
                } else {
                    echo 'Rp ' . number_format(\$num, 0, ',', '.');
                }
            ?>";
        });

        View::composer('components.navbar', function ($view) {
            $cities = Property::where('status', 'PUBLISHED')
                        ->select('city')
                        ->distinct()
                        ->orderBy('city')
                        ->pluck('city'); 

            $agencies = \App\Models\Agency::has('agents')->get();

            $view->with('cities', $cities)->with('agencies', $agencies);
            
        });

        PropertyMedia::observe(PropertyMediaObserver::class);
        Blade::component('eo.layouts.eo-layout', 'eo-layout');
        Blade::component('eo.components.eo-navbar', 'eo-navbar');
        Blade::component('eo.components.eo-footer', 'eo-footer');
    }
}
