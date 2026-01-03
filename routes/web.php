<?php

use App\Http\Controllers\GeneratePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/generate_pdf', GeneratePdfController::class);

// Add route for exam PDF download
Route::get('/exam/{code}/download-pdf', [GeneratePdfController::class, 'downloadByCode']);
