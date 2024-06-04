<?php

use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\AuthOwnerController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CancelledBookingController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ParkingSpotsController;
use App\Http\Controllers\PaymentParkingController;
use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('logout', [AuthUserController::class, 'logout']);
    Route::post('login', [AuthUserController::class, 'login']);
    Route::post('register', [AuthUserController::class, 'register']);
    Route::post('sociallogin', [AuthUserController::class, 'sociallogin']);

    Route::post('forgot-password', [ForgotPasswordController::class, 'forgot']);
    Route::post('password/reset', [ForgotPasswordController::class, 'reset']);
    Route::post('change-password', [ForgotPasswordController::class, 'changepassword']);
    

    //Parking Owner Routes
    Route::post('adminlogin', [AuthOwnerController::class, 'login']);
    Route::post('adminregister', [AuthOwnerController::class, 'register']);
    Route::post('ownersociallogin', [AuthOwnerController::class, 'sociallogin']);

    //Parking Owner Routes
    Route::post('superadminlogin', [AuthAdminController::class, 'login']);
    Route::post('superadminregister', [AuthAdminController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('user', [AuthUserController::class, 'user']);
    });
});

// Route::group(['middleware' => 'auth:sanctum'], function() {
//     Route::get('parking-spots', [ParkingSpotsController::class, 'index']);
//     Route::post('parking-spots', [ParkingSpotsController::class, 'create']);
//     Route::put('parking-spots', [ParkingSpotsController::class, 'update']);
//     Route::post('getParkingSpotsByDateTime', [ParkingSpotsController::class, 'getDateTime']);
//     Route::get('booking-detail/{id}', [ParkingSpotsController::class, 'getBookingDetail']);
//   });

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('owner-parking-spots', [ParkingSpotsController::class, 'create']);
    Route::get('owner-parking-spots', [ParkingSpotsController::class, 'ownerindex']);
    Route::put('owner-parking-spots', [ParkingSpotsController::class, 'ownerupdate']);

    Route::get('admin-parking-spots', [ParkingSpotsController::class, 'index']);
    Route::put('admin-parking-spots', [ParkingSpotsController::class, 'adminupdate']);
    Route::get('admin-owner-payment', [ParkingSpotsController::class, 'ownerpaymentIndex']);
    Route::get('owner-bookings', [BookingController::class, 'ownerindex']);
    Route::get('owner-payment-received', [BookingController::class, 'ownerpaymentreceivedindex']);
    Route::get('owner-details', [ParkingSpotsController::class, 'ownerdetailindex']);
});
Route::post('add-booking', [BookingController::class, 'store']);

Route::post('cancel-booking', [CancelledBookingController::class, 'store']);

Route::post('parking-spots', [ParkingSpotsController::class, 'create']);
Route::get('parking-spots', [ParkingSpotsController::class, 'index']);

Route::post('getParkingSpotsByDateTime', [ParkingSpotsController::class, 'getDateTime']);
Route::get('getParkingSpots', [ParkingSpotsController::class, 'getParkingSpots']);
Route::get('booking-detail/{id}', [ParkingSpotsController::class, 'getBookingDetail']);
Route::post('booking-validate', [ParkingSpotsController::class, 'bookingValidate']);
Route::any('payment-booking', [PaymentParkingController::class, 'getPaymentBooking']);
Route::any('payment-return', [PaymentParkingController::class, 'getPaymentReturn'])->name('booking.payment.show');
Route::any('payment-refund', [PaymentParkingController::class, 'getPaymentRefund']);
//payment-booking

//Admin parking
Route::delete('parking-spots/{id}', [ParkingSpotsController::class, 'destroy']);
Route::put('parking-spots/{id}', [ParkingSpotsController::class, 'update']);
Route::put('parking-spots-edit/{id}', [ParkingSpotsController::class, 'edit']);
Route::put('payment-details-edit/{id}', [ParkingSpotsController::class, 'paymentedit']);
Route::put('payment-details-add', [ParkingSpotsController::class, 'paymentadd']);
Route::get('bookings', [BookingController::class, 'index']);
Route::put('bookings/{id}', [BookingController::class, 'update']);

Route::put('refund-cancel-booking/{id}', [CancelledBookingController::class, 'update']);
Route::get('list-cancel-booking', [CancelledBookingController::class, 'index']);

Route::post('store', [DataController::class, 'store']);
Route::get('retrieve', [DataController::class, 'retrieve']);


