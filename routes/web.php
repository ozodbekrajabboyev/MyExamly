<?php

use App\Http\Controllers\GeneratePdfController;
use Illuminate\Support\Facades\Route;
use Norbek\Aivent\Facades\Aivent;


Route::get('/test', function (){
    $result = Aivent::validateCertificate('teacher-documents/milliy-sertifikat1/01K5PKJSMWW8AYJAHGX0XC91NQ.pdf');
    dd($result);
});

Route::get('/generate_pdf', GeneratePdfController::class);

// Add route for exam PDF download
Route::get('/exam/{code}/download-pdf', [GeneratePdfController::class, 'downloadByCode']);
