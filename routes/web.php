<?php

use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Agent\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\Inquiry;
use Illuminate\Http\Request;

// The Homepage
Route::get('/', [PublicController::class, 'index'])->name('home');

// The Property Detail Page
Route::get('/property/{id}/{slug}', [App\Http\Controllers\PublicController::class, 'show'])
    ->name('property.show');

// 360 Tour Route
Route::get('/property/{id}/{slug}/360', [App\Http\Controllers\PublicController::class, 'tour'])
    ->name('property.tour');

Route::get('/agent/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/agent/register', [AuthController::class, 'register']);

Route::get('/agency/{slug}', [App\Http\Controllers\PublicController::class, 'agency'])->name('agency.show');

Route::get('/agent/{id}', [App\Http\Controllers\PublicController::class, 'agent'])->name('agent.show');

// Map Search Page
Route::get('/map', [App\Http\Controllers\PublicController::class, 'mapSearch'])->name('map.search');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('agent.dashboard');
});

Route::get('/property/{id}/{slug}/pdf', [App\Http\Controllers\PublicController::class, 'downloadPdf'])
    ->name('property.pdf');

Route::view('/wishlist', 'wishlist')->name('wishlist');

Route::get('/wishlist/cards', [App\Http\Controllers\PublicController::class, 'wishlistParams'])->name('wishlist.data');

Route::get('/compare', [App\Http\Controllers\PublicController::class, 'compare'])->name('compare');

Route::post('/property/{id}/inquire', function (Request $request, $id) {
    Inquiry::create([
        'property_id' => $id,
        'buyer_name' => $request->name,
        'buyer_phone' => $request->phone,
        'message' => $request->message,
    ]);
    
    return back()->with('success', 'Message sent to agent!');
})->name('inquiry.send')->middleware('throttle:3,1');

