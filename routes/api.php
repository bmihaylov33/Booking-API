<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes for Rooms
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'getRoom']);
Route::get('/rooms/available', [RoomController::class, 'getAvailableRooms']);
// Routes for Bookings
Route::get('/bookings', [BookingController::class, 'index']);
// Routes for Customers
Route::get('/customers', [CustomerController::class, 'index']);

// Routes that require authentication
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Routes for Rooms
    Route::post('/rooms', [RoomController::class, 'store']);
    // Routes for Bookings
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::delete('/bookings/{id}', [BookingController::class, 'cancelBooking']);
    // Routes for Customers
    Route::post('/customers', [CustomerController::class, 'store']);
    // Routes for Payments
    Route::post('/payments', [PaymentController::class, 'store']);
});
