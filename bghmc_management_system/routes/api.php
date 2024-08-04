<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Define CRUD routes for patients and admissions
Route::apiResource('patients', PatientController::class);
Route::apiResource('admissions', AdmissionController::class);
Route::get('patients', [PatientController::class, 'index']);
// Define routes for authentication
Route::post('/login', [AuthController::class, 'login']); 

Route::patch('/admissions/{admission}/discharge', [AdmissionController::class, 'discharge']);


Route::get('admissions', [AdmissionController::class, 'index']);
Route::post('admissions', [AdmissionController::class, 'store']);
Route::patch('admissions/{id}/discharge', [AdmissionController::class, 'discharge']);


