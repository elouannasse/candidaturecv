<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CompetenceController;
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
    Route::post('updateProfile', [JWTAuthController::class, 'updateProfile']);
    Route::post('/offres/{offre_id}/apply', [OffreController::class, 'apply']);
    Route::post('refresh', [JWTAuthController::class, 'refresh']);
    Route::get('/user/competences', [CompetenceController::class, 'getUserCompetences']);

    Route::get('/offres/usersapplication',[OffreController::class,'usersapplication']);
});

// JWT ------------------------------------

Route::middleware(JwtMiddleware::class)->group(function () {
    Route::apiResource('/offres', OffreController::class);
});

Route::get('/user/applications', [UserController::class, 'userApplications']);


Route::get('/userss/offrescontientUser/{offre_id}', [OffreController::class, 'offrescontientUser']);


Route::get('export-excel', [OffreController::class, 'export_excel']);

Route::get('/export_applications', [OffreController::class, 'export_applications']);
