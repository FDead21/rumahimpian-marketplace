<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View; 
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Observers\PropertyMediaObserver;

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
    }
}
