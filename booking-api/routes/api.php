<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/bookings',[BookingController::class,'index']);
    Route::post('/bookings',[BookingController::class,'create']);
    Route::delete('/bookings/{booking}',[BookingController::class,'destroy']);
    Route::put('/bookings/{Booking}', [BookingController::class,'update']);
    Route::get('/bookings/{booking}',    [BookingController::class, 'show']);
});
