<?php

use App\Http\Controllers\Auth\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::group(["prefix" => "/auth"], function () {
    Route::post("/register", [UserAuthController::class, 'register'])->name("user.register");
    Route::post("/login", [UserAuthController::class, 'login'])->name("user.login");

    Route::group(["middleware" => "auth:api"], function () {
        Route::get("/me", [UserAuthController::class, 'me'])->name("user.me");
        Route::get("/logout", [UserAuthController::class, 'logout'])->name("user.logout");
    });
});
