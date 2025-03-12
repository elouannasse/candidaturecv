<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\OffreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::apiResource('offres',OffreController::class);


Route::post('/auth/register',[UserController::class,'createUser']);
Route::post('/auth/login',[UserController::class,'loginUser']);


Route::middleware('auth:sanctum')->post('/auth/updateProfile', [UserController::class, 'updateProfile']);



Route::apiResource('/offres',OffreController::class);
Route::middleware('auth:sanctum')->post('/offres/{offre_id}/apply', [OffreController::class, 'apply']);

Route::middleware('auth:sanctum')->get('/user/applications', [UserController::class, 'userApplications']);
