<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Vendor;
use App\Models\GalleryEvent;

class HomeController extends EoBaseController
{
     // Home
    public function index()
    {
        $packages      = Package::where('is_active', true)->where('is_featured', true)->take(3)->get();
        $galleryEvents = GalleryEvent::where('is_published', true)->latest('event_date')->take(4)->get();
        $vendors       = Vendor::where('is_active', true)->get();

        $eoSettings = Setting::forGroup('EO');
 
        return view('eventOrganizer.home', compact('packages', 'galleryEvents', 'vendors', 'eoSettings'));
    }
}
