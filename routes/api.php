<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\OffreController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::apiResource('offres',OffreController::class);



// sunctum ------------------------------------
// Route::post('/auth/register',[UserController::class,'createUser']);
// Route::post('/auth/login',[UserController::class,'loginUser']);
// Route::post('/auth/updateProfile', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
// sunctum ------------------------------------



// JWT ------------------------------------

Route::post('register', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);




Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::post('/offres/{offre_id}/apply', [OffreController::class, 'apply']);
});

// JWT ------------------------------------

Route::middleware(JwtMiddleware::class)->group(function () {
    Route::apiResource('/offres', OffreController::class);
});

Route::get('/user/applications', [UserController::class, 'userApplications']);
