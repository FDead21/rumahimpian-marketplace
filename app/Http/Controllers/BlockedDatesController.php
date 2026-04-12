<?php

namespace App\Http\Controllers;

use App\Models\AdminCalendarNote;
use App\Models\TourBooking;
use App\Models\Booking;
use App\Models\RentalBooking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlockedDatesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $scope = $request->query('scope', 'ALL');

        // Admin blocks (holiday, maintenance, etc)
        $adminBlocks = AdminCalendarNote::where('type', 'BLOCK')
            ->whereIn('scope', ['ALL', strtoupper($scope)])
            ->get()
            ->mapWithKeys(fn($note) => [
                $note->date->format('Y-m-d') => [
                    'reason' => $note->description,
                    'type'   => 'admin', // 🔴 red dot
                ]
            ]);

        // Booking-based full blocks per scope
        $bookingBlocks = collect();

        if ($scope === 'TOUR') {
            $bookingBlocks = TourBooking::whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])
                ->selectRaw('tour_date as date, SUM(participants) as total')
                ->groupBy('tour_date')
                ->get()
                ->filter(fn($row) => $row->total > 0)
                ->mapWithKeys(fn($row) => [
                    $row->date->format('Y-m-d') => [
                        'reason' => 'Fully booked',
                        'type'   => 'booked', // 🟠 orange dot
                    ]
                ]);
        }

        if ($scope === 'EVENT') {
            $bookingBlocks = Booking::whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])
                ->selectRaw('event_date as date, COUNT(*) as total')
                ->groupBy('event_date')
                ->get()
                ->mapWithKeys(fn($row) => [
                    $row->date->format('Y-m-d') => [
                        'reason' => 'Fully booked',
                        'type'   => 'booked',
                    ]
                ]);
        }

        if ($scope === 'RENTAL') {
            // For rental we just return admin blocks — 
            // rental blocking is per-vehicle so "fully booked" 
            // doesn't apply globally
            $bookingBlocks = collect();
        }

        // Admin blocks take priority over booking blocks
        $merged = $bookingBlocks->merge($adminBlocks);

        return response()->json($merged);
    }
}