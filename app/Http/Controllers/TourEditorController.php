<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\TourHotspot;
use Illuminate\Http\Request;

class TourEditorController extends Controller
{
    public function show(Property $property)
    {
        $scenes = $property->media()
            ->where('file_type', 'VIRTUAL_TOUR_360')
            ->orderBy('sort_order')
            ->get();

        return view('tour.editor', compact('property', 'scenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_media_id' => 'required|exists:property_media,id',
            'to_media_id'   => 'required|exists:property_media,id|different:from_media_id',
            'pitch'         => 'required|numeric',
            'yaw'           => 'required|numeric',
            'label'         => 'nullable|string|max:100',
        ]);

        $hotspot = TourHotspot::create($request->only(['from_media_id', 'to_media_id', 'pitch', 'yaw', 'label']));

        return response()->json(['success' => true, 'hotspot' => $hotspot]);
    }

    public function destroy(TourHotspot $hotspot)
    {
        $hotspot->delete();
        return response()->json(['success' => true]);
    }
}