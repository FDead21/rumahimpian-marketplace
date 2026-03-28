<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class EoBaseController extends Controller
{
    protected function eoSettings(): array
    {
        return Cache::remember('eo_settings', 3600, function () {
            $settings = Setting::where('key', 'like', 'eo_%')
                ->pluck('value', 'key')
                ->toArray();

            $jsonFields = ['eo_hero_slides'];
            foreach ($jsonFields as $field) {
                if (!empty($settings[$field])) {
                    $decoded = json_decode($settings[$field], true);
                    if (is_array($decoded)) {
                        $settings[$field] = $decoded;
                    }
                }
            }

            return $settings;
        });
    }
}