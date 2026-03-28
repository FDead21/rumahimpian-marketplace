<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Setting;

class BookingController extends EoBaseController
{
    // Booking form
    public function create(Request $request)
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

        return view('eventOrganizer.booking.create', [
            'packages'   => $packages,
            'venues'     => $venues,     
            'venuesJson' => $venuesJson,   
            'eoSettings' => Setting::forGroup('EO'),
        ]);
    }

    // Store booking
    public function store(Request $request)
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

        return redirect()->route('eventOrganizer.booking.confirmation', $booking->booking_code)
            ->with('success', "Your booking {$booking->booking_code} has been received! We'll contact you shortly.");
    }

    // Booking confirmation
    public function confirmation($code)
    {
        $booking = Booking::where('booking_code', $code)
            ->with(['package', 'property'])
            ->firstOrFail();

        return view('eventOrganizer.booking.confirmation', [
            'booking'    => $booking,
            'eoSettings' => Setting::forGroup('EO'),
        ]);
    }
}
