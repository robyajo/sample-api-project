<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MarketPlaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware([JwtMiddleware::class]);
    Route::get('/refresh', 'refresh')->middleware([JwtMiddleware::class]);
    Route::get('/me', 'me')->middleware([JwtMiddleware::class]);
    Route::get('/get-session', 'sessionCheck')->middleware([JwtMiddleware::class]);
});

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/', 'index');
        Route::post('/update/{uuid}', 'update');
        Route::get('/show/{uuid}', 'show');
    });
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/show/{uuid}', 'show');
        Route::post('/update/{uuid}', 'update');
        Route::delete('/destroy/{uuid}', 'destroy');
    });
    Route::controller(ContactController::class)->prefix('contacts')->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/show/{uuid}', 'show');
        Route::post('/update/{uuid}', 'update');
        Route::patch('/restore/{uuid}', 'restore');
        Route::delete('/destroy/{uuid}', 'destroy');
        Route::delete('/force-delete/{uuid}', 'forceDelete');
    });
    Route::controller(MarketPlaceController::class)->prefix('market-place')->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/show/{uuid}', 'show');
        Route::post('/update/{uuid}', 'update');
        Route::delete('/destroy/{uuid}', 'destroy');
    });
});
