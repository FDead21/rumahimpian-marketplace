<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Vendor;
use App\Models\GalleryEvent;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EoController extends Controller
{
    // Load EO settings (cached for performance)
    private function eoSettings(): array
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

    // Home
    public function home()
    {
        $packages      = Package::where('is_active', true)->where('is_featured', true)->take(3)->get();
        $galleryEvents = GalleryEvent::where('is_published', true)->latest('event_date')->take(4)->get();
        $vendors       = Vendor::where('is_active', true)->get();

        return view('eo.home', [
            'packages'      => $packages,
            'galleryEvents' => $galleryEvents,
            'vendors'       => $vendors,
            'eoSettings'    => $this->eoSettings(),
        ]);
    }

    // Packages listing
    public function packages()
    {
        $packages = Package::where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('price')
            ->get();

        return view('eo.packages', [
            'packages'   => $packages,
            'eoSettings' => $this->eoSettings(),
        ]);
    }

    // Package detail
        public function packageShow($slug)
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
 
        return view('eo.package-detail', [
            'package'         => $package,
            'relatedPackages' => $relatedPackages,
            'eoSettings'      => $this->eoSettings(),
        ]);
    }
 

    // Booking form
    public function bookingForm(Request $request)
    {
        $packages = Package::where('is_active', true)->orderBy('price')->get();

        // Only rentable properties as venue options
        $venues = Property::where('status', 'PUBLISHED')
            ->where('listing_type', 'RENT')
            ->with('media') 
            ->orderBy('city')
            ->get();

        $venuesJson = $venues->map(function($v) {
            return [
                'id'            => $v->id,
                'title'         => $v->title,
                'city'          => $v->city,
                'district'      => $v->district ?? '',
                'property_type' => $v->property_type ?? '',
                'bedrooms'      => $v->bedrooms,
                'bathrooms'     => $v->bathrooms,
                'building_area' => $v->building_area,
                'price'         => number_format($v->price, 0, ',', '.'),
                'thumb'         => $v->media->first() 
                                    ? asset('storage/' . $v->media->first()->file_path) 
                                    : null,
            ];
        })->values();

        return view('eo.booking.booking-form', [
            'packages'   => $packages,
            'venues'     => $venues,     
            'venuesJson' => $venuesJson,   
            'eoSettings' => $this->eoSettings(),
        ]);
    }

    // Store booking
    public function bookingStore(Request $request)
    {
        $request->validate([
            'package_id'   => 'required|exists:packages,id',
            'event_type'   => 'required|string',
            'event_date'   => 'required|date|after:today',
            'client_name'  => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email',
            'guest_count'  => 'nullable|integer|min:1',
            'property_id'  => 'nullable|exists:properties,id',
            'notes'        => 'nullable|string',
        ]);

        $package = Package::findOrFail($request->package_id);

        $booking = Booking::create([
            'package_id'     => $package->id,
            'property_id'    => $request->property_id,
            'client_name'    => $request->client_name,
            'client_phone'   => $request->client_phone,
            'client_email'   => $request->client_email,
            'event_type'     => $request->event_type,
            'event_date'     => $request->event_date,
            'guest_count'    => $request->guest_count,
            'notes'          => $request->notes,
            'total_price'    => $package->price,
            'status'         => 'INQUIRY',
            'payment_status' => 'UNPAID',
        ]);

        return redirect()->route('eo.booking.booking-confirmation', $booking->booking_code)
            ->with('success', "Your booking {$booking->booking_code} has been received! We'll contact you shortly.");
    }

    // Booking confirmation
    public function bookingConfirmation($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['package', 'property'])
            ->firstOrFail();

        return view('eo.booking.booking-confirmation', [
            'booking'    => $booking,
            'eoSettings' => $this->eoSettings(),
        ]);
    }

    // Gallery listing
    public function gallery()
    {
        $galleryEvents = GalleryEvent::where('is_published', true)
            ->with('media')
            ->latest('event_date')
            ->paginate(9);

        return view('eo.gallery.gallery-index', [
            'galleryEvents' => $galleryEvents,
            'eoSettings'    => $this->eoSettings(),
        ]);
    }

    // Gallery event detail
    public function galleryShow($slug)
    {
        $event = GalleryEvent::where('slug', $slug)
            ->where('is_published', true)
            ->with('media')
            ->firstOrFail();

        return view('eo.gallery.gallery-show', [
            'event'      => $event,
            'eoSettings' => $this->eoSettings(),
        ]);
    }

    // Vendors listing
    public function vendors()
    {
        $vendors = Vendor::where('is_active', true)
            ->with('media')
            ->orderBy('category')
            ->get()
            ->groupBy('category');

        return view('eo.vendors.vendor-index', [
            'vendors'    => $vendors,
            'eoSettings' => $this->eoSettings(),
        ]);
    }
}
