<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use Norbek\Aivent\Facades\Aivent;


Route::get('/test', function (){
    $result = Aivent::validateCertificate('teacher-documents/milliy-sertifikat1/01K5PKJSMWW8AYJAHGX0XC91NQ.pdf');
    dd($result);
});
