<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Verified;
use App\Listeners\AssignBlueBadge;
use App\Models\Setting;
use App\Models\Property;
use App\Models\Agency;
use App\Models\PropertyMedia;
use App\Observers\PropertyMediaObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->bootEvents();
        $this->bootViewGlobals();
        $this->bootViewComposers();
        $this->bootBladeDirectives();
        $this->bootBladeComponents();
        $this->bootObservers();
    }

    // ----------------------------------------------------------------
    // Events
    // ----------------------------------------------------------------

    private function bootEvents(): void
    {
        Event::listen(Verified::class, AssignBlueBadge::class);
    }

    // ----------------------------------------------------------------
    // Global view data — shared with every view, one DB hit, cached forever.
    // Clear cache via Setting::flushCache() when admin saves a setting.
    // ----------------------------------------------------------------

    private function bootViewGlobals(): void
    {
        if ($this->app->runningInConsole() || ! Schema::hasTable('settings')) {
            return;
        }

        // All settings as flat array — views access via $settings['site_name']
        // EO views access via $settings['eo_site_name'] — same array, no duplication.
        // Now that settings have a 'group' column, Filament can filter by group.
        // The flat array here stays for backwards compatibility with existing views.
        $settings = Setting::allCached();

        View::share('settings', $settings);
    }

    // ----------------------------------------------------------------
    // View Composers — scoped, not global
    // ----------------------------------------------------------------

    private function bootViewComposers(): void
    {
        // Navbar needs cities + agencies — only load for views that use the navbar
        View::composer('components.navbar', function ($view) {
            $cities = Property::where('status', 'PUBLISHED')
                ->select('city')
                ->distinct()
                ->orderBy('city')
                ->pluck('city');

            $agencies = Agency::has('agents')->get();

            $view->with(compact('cities', 'agencies'));
        });

        // NOTE: eoSettings is intentionally removed from the global View::composer.
        // EO controllers that need eo-specific settings now call Setting::forGroup('EO')
        // directly. This avoids running EO queries on property pages.
    }

    // ----------------------------------------------------------------
    // Blade Directives
    // ----------------------------------------------------------------

    private function bootBladeDirectives(): void
    {
        Blade::directive('currency', function ($expression) {
            return "<?php
                \$_num = (float)($expression);
                if (\$_num >= 1_000_000_000) {
                    echo 'Rp ' . number_format(\$_num / 1_000_000_000, 1, ',', '.') . ' Miliar';
                } elseif (\$_num >= 1_000_000) {
                    echo 'Rp ' . number_format(\$_num / 1_000_000, 1, ',', '.') . ' Juta';
                } else {
                    echo 'Rp ' . number_format(\$_num, 0, ',', '.');
                }
            ?>";
        });
    }

    // ----------------------------------------------------------------
    // Blade Components
    // ----------------------------------------------------------------

    private function bootBladeComponents(): void
    {
        Blade::component('eventOrganizer.components.layout', 'eo-layout');
        Blade::component('eventOrganizer.components.eo-navbar', 'eo-navbar');
        Blade::component('eventOrganizer.components.eo-footer', 'eo-footer');
    }

    // ----------------------------------------------------------------
    // Observers
    // ----------------------------------------------------------------

    private function bootObservers(): void
    {
        PropertyMedia::observe(PropertyMediaObserver::class);
    }
}