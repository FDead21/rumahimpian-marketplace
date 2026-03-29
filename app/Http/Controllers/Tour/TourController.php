<?php

namespace App\Http\Controllers\Tour;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends TourBaseController
{
    public function index(Request $request)
    {
        $query = Tour::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('meeting_point', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', strtoupper($request->category));
        }

        if ($request->filled('duration')) {
            $query->where('duration_days', $request->duration);
        }

        $tours = $query->orderByDesc('is_featured')
            ->orderBy('price_per_person')
            ->with('media')
            ->get();

        return view('tour.tour.index', compact('tours'));
    }

    public function show(string $slug)
    {
        $tour = Tour::where('slug', $slug)
            ->where('is_active', true)
            ->with(['media', 'user'])
            ->firstOrFail();

        $related = Tour::where('is_active', true)
            ->where('id', '!=', $tour->id)
            ->where('category', $tour->category)
            ->orderByDesc('is_featured')
            ->take(3)
            ->with('media')
            ->get();

        return view('tour.tour.show', compact('tour', 'related'));
    }
}