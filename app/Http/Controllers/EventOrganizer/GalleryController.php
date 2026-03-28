<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GalleryEvent;
use App\Models\Setting;

class GalleryController extends EoBaseController
{
     // Gallery listing
    public function index()
    {
        $galleryEvents = GalleryEvent::where('is_published', true)
            ->with('media')
            ->latest('event_date')
            ->paginate(9);

        return view('eventOrganizer.gallery.index', [
            'galleryEvents' => $galleryEvents,
            'eoSettings' => Setting::forGroup('EO'),
        ]);
    }

    // Gallery event detail
    public function show($slug)
    {
        $event = GalleryEvent::where('slug', $slug)
            ->where('is_published', true)
            ->with('media')
            ->firstOrFail();

        return view('eventOrganizer.gallery.show', [
            'event'      => $event,
            'eoSettings' => Setting::forGroup('EO'),
        ]);
    }
}
