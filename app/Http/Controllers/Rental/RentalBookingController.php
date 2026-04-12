<?php

namespace App\Http\Controllers\Rental;

use App\Models\RentalVehicle;
use App\Models\RentalBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RentalBookingController extends RentalBaseController
{
    public function create(string $slug)
    {
        $vehicle = RentalVehicle::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // 1. Automatic Blocked Dates (from active bookings)
        $activeBookings = RentalBooking::where('rental_vehicle_id', $vehicle->id)
            ->whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])
            ->get(['start_date', 'end_date']);

        $autoBlocked = [];
        foreach ($activeBookings as $booking) {
            $period = \Carbon\CarbonPeriod::create($booking->start_date, $booking->end_date);
            foreach ($period as $date) {
                $autoBlocked[] = $date->format('Y-m-d');
            }
        }

        // 2. Manual Blocked Dates (from Filament Admin)
        $manualBlocked = array_merge(
            $vehicle->blocked_dates ?? [],
            \App\Models\AdminCalendarNote::where('type', 'BLOCK')
                ->pluck('date')
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray()
        );

        // 3. Merge them together!
        $blockedDates = array_unique(array_merge($autoBlocked, $manualBlocked));

        return view('rental.booking.create', compact('vehicle', 'blockedDates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rental_vehicle_id' => 'required|exists:rental_vehicles,id',
            'dates'           => 'required|string',
            'client_name'     => 'required|string|max:255',
            'client_phone'    => 'required|string|max:20',
            'client_email'    => 'nullable|email',
        ]);

        $dateParts = explode(' to ', $request->dates);
        $startDate = \Carbon\Carbon::parse($dateParts[0])->format('Y-m-d');
        $endDate = isset($dateParts[1]) 
            ? \Carbon\Carbon::parse($dateParts[1])->format('Y-m-d') 
            : $startDate;

        // Check every date in the range against admin blocks
        $adminBlocked = \App\Models\AdminCalendarNote::where('type', 'BLOCK')
            ->whereIn('scope', ['ALL', 'RENTAL'])  // ✅
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        if ($adminBlocked) {
            return back()->withErrors([
                'dates' => 'Your selected range includes an unavailable date (' 
                    . $adminBlocked->date->format('d M Y') 
                    . '): ' . $adminBlocked->description
            ])->withInput();
        }

        $vehicle = RentalVehicle::findOrFail($request->rental_vehicle_id);

        // 2. Calculate Total Price (Inclusive of start and end day)
        $days = $startDate->diffInDays($endDate) + 1; 
        $totalPrice = $days * $vehicle->price_per_day;

        // 3. Create the Booking
        $booking = RentalBooking::create([
            'rental_vehicle_id' => $vehicle->id,
            'client_name'     => $request->client_name,
            'client_phone'    => $request->client_phone,
            'client_email'    => $request->client_email,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'total_price'     => $totalPrice,
            'status'          => 'INQUIRY',
            'payment_status'  => 'UNPAID',
        ]);

        // 4. Redirect to Receipt
        return redirect()->route('rental.booking.confirmation', $booking->booking_code);
    }

    public function confirmation($booking_code)
    {
        $booking = RentalBooking::with('rentalVehicle')->where('booking_code', $booking_code)->firstOrFail();
        return view('rental.booking.confirmation', compact('booking'));
    }
}