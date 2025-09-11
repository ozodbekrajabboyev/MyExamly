<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;


Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});
Route::post('/contact', ContactController::class);
