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

        $autoBlocked = [];

        if ($tour->max_participants) {
            // Find dates where the SUM of participants is >= the tour's max capacity
            $fullyBookedDates = TourBooking::select('tour_date', \Illuminate\Support\Facades\DB::raw('SUM(participants) as total_pax'))
                ->where('tour_id', $tour->id)
                ->whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])
                ->groupBy('tour_date')
                ->having('total_pax', '>=', $tour->max_participants)
                ->get();

            foreach ($fullyBookedDates as $booking) {
                $autoBlocked[] = $booking->tour_date->format('Y-m-d');
            }
        }
        
        // Use $tour, not $vehicle!
        $manualBlocked = array_merge(
            $tour->blocked_dates ?? [],
            \App\Models\AdminCalendarNote::where('type', 'BLOCK')
                ->pluck('date')
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray()
        );

        // Merge them together
        $blockedDates = array_unique(array_merge($autoBlocked, $manualBlocked));

        return view('tour.booking.create', compact('tour', 'blockedDates'));
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

        $adminBlocked = \App\Models\AdminCalendarNote::where('type', 'BLOCK')
            ->whereIn('scope', ['ALL', 'TOUR'])  // ✅
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        if (in_array($request->tour_date, $adminBlocked)) {
            $reason = \App\Models\AdminCalendarNote::where('type', 'BLOCK')
                ->whereDate('date', $request->tour_date)
                ->value('description');
            return back()->withErrors([
                'tour_date' => 'This date is unavailable: ' . $reason
            ])->withInput();
        }

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