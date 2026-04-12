<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Core & Auth Controllers
use App\Http\Controllers\Core\AuthController;
use App\Http\Controllers\Core\HomeController as SuperAppHomeController;
use App\Http\Controllers\Core\ArticleController;

// Property Domain Controllers
use App\Http\Controllers\Property\HomeController as PropertyHomeController;
use App\Http\Controllers\Property\PropertyController;
use App\Http\Controllers\Property\MapSearchController;
use App\Http\Controllers\Property\AgentController;
use App\Http\Controllers\Property\AgencyController;
use App\Http\Controllers\Property\InquiryController;
use App\Http\Controllers\Property\TourEditorController;
use App\Http\Controllers\Property\DashboardController;

// Tour Controllers
use App\Http\Controllers\Tour\HomeController as TourHomeController;
use App\Http\Controllers\Tour\TourController;
use App\Http\Controllers\Tour\BookingController as TourBookingController;

// Rental Controllers
use App\Http\Controllers\Rental\HomeController as RentalHomeController;
use App\Http\Controllers\Rental\VehicleController;
use App\Http\Controllers\Rental\RentalBookingController;

// Event Organizer Controllers
use App\Http\Controllers\EventOrganizer\HomeController as EventOrganizerHomeController;
use App\Http\Controllers\EventOrganizer\PackageController;
use App\Http\Controllers\EventOrganizer\BookingController;
use App\Http\Controllers\EventOrganizer\GalleryController;
use App\Http\Controllers\EventOrganizer\VendorController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [SuperAppHomeController::class, 'index'])->name('home');

Route::get('/news', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/news/{slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        Session::put('locale', $locale);
    }
    return back();
})->name('lang.switch');

Route::get('/api/blocked-dates', [\App\Http\Controllers\BlockedDatesController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Property Routes
| Prefix: /property
| Names:  property.*
|--------------------------------------------------------------------------
*/

Route::prefix('property')->name('property.')->group(function () {

    Route::get('/', [PropertyHomeController::class, 'index'])->name('home');

    Route::get('/wishlist', fn() => view('property.wishlist'))->name('wishlist');
    Route::get('/wishlist/cards', [PropertyController::class, 'wishlistParams'])->name('wishlist.data');
    Route::get('/compare', [PropertyController::class, 'compare'])->name('compare');
    Route::get('/map', [MapSearchController::class, 'index'])->name('map');

    Route::get('/agent/{id}', [AgentController::class, 'show'])->name('agent.show');
    Route::get('/agency/{slug}', [AgencyController::class, 'show'])->name('agency.show');

    Route::get('/{id}/{slug}', [PropertyController::class, 'show'])->name('show');
    Route::get('/{id}/{slug}/360', [PropertyController::class, 'tour'])->name('tour');
    Route::get('/{id}/{slug}/pdf', [PropertyController::class, 'downloadPdf'])->name('pdf');

    Route::post('/{id}/inquire', [InquiryController::class, 'store'])
        ->name('inquiry.store')
        ->middleware('throttle:3,1');
});

/*
|--------------------------------------------------------------------------
| Event Organizer Routes
| Prefix: /event-organizer  (kebab-case, consistent with Laravel convention)
| Names:  eo.*
|--------------------------------------------------------------------------
*/

Route::prefix('eventOrganizer')->name('eventOrganizer.')->group(function () {

    Route::get('/', [EventOrganizerHomeController::class, 'index'])->name('home');

    // Packages
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/{slug}', [PackageController::class, 'show'])->name('packages.show');

    // Booking
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:5,1');
    Route::get('/booking/confirmation/{code}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

    // Gallery
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/{slug}', [GalleryController::class, 'show'])->name('gallery.show');

    // Vendors
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/{id}', [VendorController::class, 'show'])->name('vendors.show');
});

/*
|--------------------------------------------------------------------------
| Tour Routes
| Prefix: /tour
| Names:  tour.*
|--------------------------------------------------------------------------
*/

Route::prefix('tour')->name('tour.')->group(function () {

    Route::get('/', [TourHomeController::class, 'index'])->name('home');

    Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
    Route::get('/tours/{slug}', [TourController::class, 'show'])->name('tours.show');

    Route::get('/booking', [TourBookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [TourBookingController::class, 'store'])->name('booking.store')->middleware('throttle:5,1');
    Route::get('/booking/confirmation/{code}', [TourBookingController::class, 'confirmation'])->name('booking.confirmation');
});

/*
|--------------------------------------------------------------------------
| Rental Routes
| Prefix: /rental
| Names:  rental.*
|--------------------------------------------------------------------------
*/

Route::prefix('rental')->name('rental.')->group(function () {

    Route::get('/', [RentalHomeController::class, 'index'])->name('home');

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/{slug}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/rental/book/{slug}', [RentalBookingController::class, 'create'])->name('booking.create');
    Route::post('/rental/book', [RentalBookingController::class, 'store'])->name('booking.store');
    Route::get('/rental/receipt/{booking_code}', [RentalBookingController::class, 'confirmation'])->name('booking.confirmation');
});

/*
|--------------------------------------------------------------------------
| Authentication & Agent Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/agent/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/agent/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('agent.dashboard');
});

/*
|--------------------------------------------------------------------------
| Email Verification
|--------------------------------------------------------------------------
*/

Route::get('/email/verify', fn() => view('auth.verify-email'))
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard')->with('success', 'Email verified! You now have the Blue Badge.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Portal API — Virtual Tour Editor (auth required)
|--------------------------------------------------------------------------
*/

Route::prefix('portal-api')->name('portal.')->middleware('auth')->group(function () {
    Route::get('/properties/{property}/tour-editor', [TourEditorController::class, 'show'])->name('tour.editor');
    Route::post('/hotspots', [TourEditorController::class, 'store'])->name('hotspots.store');
    Route::delete('/hotspots/{hotspot}', [TourEditorController::class, 'destroy'])->name('hotspots.destroy');
});