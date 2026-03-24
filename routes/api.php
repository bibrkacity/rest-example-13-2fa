<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Middleware\Checking2fa;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::name('api.v1.')
    ->prefix('v1')
    ->group(function () {

        Route::post('/login', [AuthController::class, 'login'])
            ->name('login');

        Route::middleware(['auth:sanctum'])
            ->group(function () {
                Route::controller(AuthController::class)
                    ->prefix('auth')
                    ->name('auth.')
                    ->group(function () {
                        Route::get('/user', 'getUser')->name('user');
                        Route::get('/logout', 'logout')->name('logout');
                        Route::post('/enable-2fa', 'enable2fa')->name('enable-2fa');
                        Route::post('/verify-2fa', 'verify2fa')->name('verify-2fa');
                        Route::post('/verify-2fa-login', 'verify2faLogin')->name('verify-2fa-login');
                    });
            });

        Route::middleware(['auth:sanctum', Checking2fa::class, SetLocale::class])
            ->group(function () {
                Route::controller(UserController::class)
                    ->prefix('users')
                    ->name('users.')
                    ->group(function () {
                        Route::get('', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::get('/{id}', 'show')->name('show');
                        Route::put('/{id}', 'update')->name('update');
                        Route::delete('/{id}', 'destroy')->name('destroy');
                    });
            });
    });
