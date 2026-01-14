<?php

use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Agent\DashboardController;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


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

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        Session::put('locale', $locale);
    }
    return back();
})->name('lang.switch');

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

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

// 1. The Notice Page (Shown if they try to access dashboard without verifying)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. The Link Handler (When they click the email link)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // This fires the "Verified" event -> triggers our Listener!
    return redirect('/dashboard')->with('success', 'Email verified! You now have the Blue Badge.');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. Resend Link
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');