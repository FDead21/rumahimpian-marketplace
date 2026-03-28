<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Setting;

class VendorController extends EoBaseController
{
     // Vendors listing
    public function index()
    {
        $vendors = Vendor::where('is_active', true)
            ->with('media')
            ->orderBy('category')
            ->get()
            ->groupBy('category');

        return view('eventOrganizer.vendors.vendor-index', [
            'vendors'    => $vendors,
            'eoSettings' => Setting::forGroup('EO'),
        ]);
    }
}
