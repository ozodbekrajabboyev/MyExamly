<?php

use App\Http\Controllers\DistrictController;
use App\Http\Controllers\MaktabController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SinfController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;


Route::middleware('telegram.api-key')->group(function () {
    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/regions/{id}/districts', [DistrictController::class, 'byRegion']);
    Route::get('districts/{district}/schools', [MaktabController::class, 'byDistrict']);
    Route::get('schools/{school}/classes', [SinfController::class, 'bySchool']);
    Route::get('classes/{class}/students', [StudentController::class, 'byClass']);
    Route::get('/students/{student}/result', [StudentController::class, 'result']);
});

