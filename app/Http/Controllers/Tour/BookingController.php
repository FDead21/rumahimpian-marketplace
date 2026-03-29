<?php

namespace App\Http\Controllers\Tour;

use App\Models\Tour;
use App\Models\TourBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends TourBaseController
{
    public function create(Request $request)
    {
        $tour = Tour::where('is_active', true)
            ->with('media')
            ->findOrFail($request->tour_id);

        return view('tour.booking.create', compact('tour'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tour_id'      => 'required|exists:tours,id',
            'client_name'  => 'required|string|max:255',
            'client_phone' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'tour_date'    => 'required|date|after:today',
            'participants' => 'required|integer|min:1',
            'notes'        => 'nullable|string',
        ]);

        $tour = Tour::findOrFail($request->tour_id);

        // Validate participant count against tour limits
        if ($tour->max_participants && $request->participants > $tour->max_participants) {
            return back()->withErrors(['participants' => "Maximum {$tour->max_participants} participants allowed."])->withInput();
        }

        if ($request->participants < $tour->min_participants) {
            return back()->withErrors(['participants' => "Minimum {$tour->min_participants} participants required."])->withInput();
        }

        $booking = TourBooking::create([
            'booking_code'  => 'TOUR-' . strtoupper(Str::random(8)),
            'tour_id'       => $tour->id,
            'client_name'   => $request->client_name,
            'client_phone'  => $request->client_phone,
            'client_email'  => $request->client_email,
            'tour_date'     => $request->tour_date,
            'participants'  => $request->participants,
            'total_price'   => $tour->price_per_person * $request->participants,
            'notes'         => $request->notes,
            'status'        => 'INQUIRY',
            'payment_status' => 'UNPAID',
        ]);

        return redirect()->route('tour.booking.confirmation', $booking->booking_code);
    }

    public function confirmation(string $code)
    {
        $booking = TourBooking::where('booking_code', $code)
            ->with('tour')
            ->firstOrFail();

        return view('tour.booking.confirmation', compact('booking'));
    }
}