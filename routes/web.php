<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use Norbek\Aivent\Facades\Aivent;


Route::get('/', function (){
    $result = Aivent::validateCertificate('Milliy.pdf');
//    dd($result);
});
