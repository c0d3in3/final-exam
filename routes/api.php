<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/countries/list', [CountryController::class, 'getCountries'])->middleware('auth:sanctum');
Route::get('/countries/saved', [CountryController::class, 'getSavedCountries'])->middleware('auth:sanctum');
Route::post('/countries/save-country', [CountryController::class, 'saveCountry'])->middleware('auth:sanctum');
Route::delete('/countries/delete-saved-country', [CountryController::class, 'deleteSavedCountry'])->middleware('auth:sanctum');
Route::get('/countries/world', [CountryController::class, 'getWorldStats'])->middleware('auth:sanctum');
