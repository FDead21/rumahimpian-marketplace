<?php

use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Agent\DashboardController;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [PublicController::class, 'index'])->name('home');

// Map Search
Route::get('/map', [PublicController::class, 'mapSearch'])->name('map.search');

// Agent & Agency Profiles
Route::get('/agent/{id}', [PublicController::class, 'agent'])->name('agent.show');
Route::get('/agency/{slug}', [PublicController::class, 'agency'])->name('agency.show');

/*
|--------------------------------------------------------------------------
| Property Routes
|--------------------------------------------------------------------------
*/

Route::prefix('property')->group(function () {
    Route::get('/{id}/{slug}', [PublicController::class, 'show'])->name('property.show');
    Route::get('/{id}/{slug}/360', [PublicController::class, 'tour'])->name('property.tour');
    Route::get('/{id}/{slug}/pdf', [PublicController::class, 'downloadPdf'])->name('property.pdf');
    
    // Inquiry Form Submission
    Route::post('/{id}/inquire', function (Request $request, $id) {
        Inquiry::create([
            'property_id' => $id,
            'buyer_name' => $request->name,
            'buyer_phone' => $request->phone,
            'message' => $request->message,
        ]);
        return back()->with('success', 'Message sent to agent!');
    })->name('inquiry.send')->middleware('throttle:3,1');
});

/*
|--------------------------------------------------------------------------
| User Features (Wishlist & Compare)
|--------------------------------------------------------------------------
*/

Route::view('/wishlist', 'wishlist')->name('wishlist');
Route::get('/wishlist/cards', [PublicController::class, 'wishlistParams'])->name('wishlist.data');
Route::get('/compare', [PublicController::class, 'compare'])->name('compare');

/*
|--------------------------------------------------------------------------
| Authentication & Agent Dashboard
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/agent/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/agent/register', [AuthController::class, 'register']);
});

// Authenticated Agent Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('agent.dashboard');
});