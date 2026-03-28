<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\Property;

class InquiryController extends Controller
{
    public function store(Request $request, $id)
    {
        // 1. Validate the incoming form data
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        // 2. Verify the property actually exists
        $property = Property::findOrFail($id);

        // 3. Save the lead to the database
        Inquiry::create([
            'property_id' => $property->id,
            'buyer_name'  => $request->name,
            'buyer_email' => $request->email,
            'buyer_phone' => $request->phone,
            'message'     => $request->message,
            'status'      => 'NEW',
        ]);

        // 4. Send them right back to the property page with a toast message
        return back()->with('success', 'Your inquiry has been sent! The agent will contact you shortly.');
    }
}