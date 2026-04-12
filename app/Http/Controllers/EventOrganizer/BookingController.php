<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Package;
use App\Models\Vendor;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Setting;

class BookingController extends EoBaseController
{
    /**
     * Show the dynamic booking form.
     */
    public function create(Request $request)
    {
        $selectedPackage = null;
        if ($request->has('package_id')) {
            $selectedPackage = Package::where('is_active', true)->find($request->package_id);
        }

        $packages = Package::where('is_active', true)->orderBy('name')->get();

        $vendorsByCategory = Vendor::where('is_active', true)
            ->whereNotNull('price_from') 
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $eoSettings = Setting::forGroup('EO');

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

       $adminBlocked = \App\Models\AdminCalendarNote::where('type', 'BLOCK')
            ->whereIn('scope', ['ALL', 'EVENT'])  // ✅
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        if ($adminBlocked) {
            return back()->withErrors([
                'event_date' => 'This date is unavailable: ' . $adminBlocked->description
            ])->withInput();
        }

        $autoBlocked = [];
        $manualBlocked = $adminBlocked; // Admin blocks always apply

        if ($selectedPackage) {
            // Dates blocked by confirmed bookings for this package
            $autoBlocked = Booking::where('package_id', $selectedPackage->id)
                ->whereIn('status', ['CONFIRMED', 'IN_PROGRESS', 'COMPLETED'])
                ->pluck('event_date')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->toArray();

            // Package's own manual blocked dates + admin blocked dates
            $manualBlocked = array_merge(
                $adminBlocked,
                $selectedPackage->blocked_dates ?? []
            );
        }

        $blockedDates = array_unique(array_merge($autoBlocked, $manualBlocked));

        return view('eventOrganizer.booking.create', compact(
            'selectedPackage', 
            'packages', 
            'vendorsByCategory', 
            'eoSettings',
            'venuesJson',         
            'venues',
            'blockedDates' 
        ));
    }

    /**
     * Store the booking and attach dynamic vendors.
     */
    /**
     * Store the booking and attach dynamic vendors.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name'     => 'required|string|max:255',
            'client_phone'    => 'required|string|max:20',
            'client_email'    => 'nullable|email|max:255',
            'event_date'      => 'required|date|after:today',
            'guest_count'     => 'required|integer|min:1',
            'event_type'      => 'required|string|max:100',
            'package_id'      => 'required|exists:packages,id',
            'notes'           => 'nullable|string',
            'property_id'     => 'nullable|exists:properties,id',
            // Base vendors (checkboxes)
            'vendors'         => 'nullable|array', 
            'vendors.*'       => 'exists:vendors,id',
            // Specific service items (from modals)
            'vendor_services' => 'nullable|array',
            'vendor_services.*' => 'array', 
        ]);

        try {
            DB::beginTransaction();

            // Eager load the package's bundled vendors so we can attach them later
            $package = Package::with('vendors')->findOrFail($validated['package_id']);
            $totalPrice = $package->price;

            $booking = Booking::create([
                'package_id'     => $package->id,
                'property_id'    => $validated['property_id'] ?? null,
                'client_name'    => $validated['client_name'],
                'client_phone'   => $validated['client_phone'],
                'client_email'   => $validated['client_email'],
                'event_date'     => $validated['event_date'],
                'guest_count'    => $validated['guest_count'],
                'event_type'     => $validated['event_type'],
                'notes'          => $validated['notes'],
                
                'status'         => 'INQUIRY', 
                'payment_status' => 'UNPAID',
                
                'total_price'    => 0, 
                'deposit_amount' => 0, 
            ]);

            $syncData = [];

            // 1. Handle Base Vendors (Checkboxes without specific items)
            if (!empty($validated['vendors'])) {
                $baseVendors = Vendor::whereIn('id', $validated['vendors'])->get();
                foreach ($baseVendors as $vendor) {
                    $vendorPrice = $vendor->price_from ?? 0;
                    $totalPrice += $vendorPrice;
                    $syncData[$vendor->id] = [
                        'agreed_price' => $vendorPrice,
                        'notes'        => 'Base Package selected.',
                    ];
                }
            }

            // 2. Handle Specific Service Items (from Modal)
            if (!empty($validated['vendor_services'])) {
                foreach ($validated['vendor_services'] as $vendorId => $itemIndices) {
                    $vendor = Vendor::find($vendorId);
                    if (!$vendor || !is_array($vendor->service_menu)) continue;

                    $agreedPrice = 0;
                    $notesArr = [];

                    foreach ($itemIndices as $idx) {
                        $item = $vendor->service_menu[$idx] ?? null;
                        if ($item) {
                            $itemPrice = (float) ($item['price'] ?? 0);
                            $agreedPrice += $itemPrice;
                            $notesArr[] = "- " . ($item['item_name'] ?? 'Item') . " (Rp " . number_format($itemPrice, 0, ',', '.') . ")";
                        }
                    }

                    $totalPrice += $agreedPrice;

                    // If they also checked the base vendor somehow, append to existing notes
                    if (isset($syncData[$vendor->id])) {
                        $syncData[$vendor->id]['agreed_price'] += $agreedPrice;
                        $syncData[$vendor->id]['notes'] .= "\nAdd-ons:\n" . implode("\n", $notesArr);
                    } else {
                        $syncData[$vendor->id] = [
                            'agreed_price' => $agreedPrice,
                            'notes'        => "Selected Add-ons:\n" . implode("\n", $notesArr),
                        ];
                    }
                }
            }

            // 3. THE NEW PART: Auto-attach the Package's Bundled Vendors
            if ($package->vendors) {
                foreach ($package->vendors as $bundledVendor) {
                    // Check if the user already added an extra service from this exact bundled vendor
                    if (isset($syncData[$bundledVendor->id])) {
                        // Keep their extra agreed_price, just append a note that they are a package vendor
                        $syncData[$bundledVendor->id]['notes'] = "Included in Package Bundle\n" . $syncData[$bundledVendor->id]['notes'];
                    } else {
                        // Not in the extra cart, so add them for free (cost covered by the package)
                        $syncData[$bundledVendor->id] = [
                            'agreed_price' => 0,
                            'notes'        => 'Included in Package Bundle' . ($bundledVendor->pivot->is_mandatory ? ' (Mandatory)' : ''),
                        ];
                    }
                }
            }

            // Save all vendors to the pivot table
            $booking->vendors()->sync($syncData);
            
            // Save the final calculated total price
            $booking->update(['total_price' => $totalPrice]);

            DB::commit();

            return redirect()->route('eventOrganizer.booking.confirmation', $booking->booking_code)
                             ->with('success', 'Your event has been booked successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Show the success page.
     */
    public function confirmation($code)
    {
        $booking = Booking::with(['package', 'vendors'])->where('booking_code', $code)->firstOrFail();
        
        return view('eventOrganizer.booking.confirmation', compact('booking'));
    }
}