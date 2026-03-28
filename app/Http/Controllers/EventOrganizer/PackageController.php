<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Setting;

class PackageController extends EoBaseController
{
     // Packages listing
    public function index()
    {
        $packages = Package::where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('price')
            ->get();

        return view('eventOrganizer.package.packages', [
            'packages'   => $packages,
            'eoSettings' => Setting::forGroup('EO'),
        ]);
    }

    // Package detail
        public function show($slug)
    {
        $package = Package::where('slug', $slug)
            ->where('is_active', true)
            ->with(['vendor', 'media'])
            ->firstOrFail();
 
        $relatedPackages = Package::where('is_active', true)
            ->where('id', '!=', $package->id)
            ->orderByDesc('is_featured')
            ->orderByRaw('ABS(price - ?)', [$package->price])
            ->take(3)
            ->with('media')
            ->get();
 
        return view('eventOrganizer.package.package-detail', [
            'package'         => $package,
            'relatedPackages' => $relatedPackages,
            'eoSettings' => Setting::forGroup('EO'),
        ]);
    }
}
