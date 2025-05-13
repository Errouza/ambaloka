<?php

use App\Http\Controllers\TransportController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('transports.index');
});

// Login dan registrasi
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Transportasi
Route::get('/transports', [TransportController::class, 'index'])->name('transports.index');
Route::get('/transports/search', [TransportController::class, 'search'])->name('transports.search');
Route::get('/transports/{id}', [TransportController::class, 'show'])->name('transports.show');
Route::post('/transports/{id}/book', [TransportController::class, 'book'])->name('transports.book')->middleware('auth');

// Hotel
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/search', [HotelController::class, 'search'])->name('hotels.search');
Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');
Route::post('/hotels/{id}/book', [HotelController::class, 'book'])->name('hotels.book')->middleware('auth');

// Pesanan
Route::middleware('auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
});
