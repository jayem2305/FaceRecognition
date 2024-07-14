<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaceRecognitionController;


Route::get('/', function () {
    return view('welcome');
});
Route::post('/scan-face', [FaceRecognitionController::class, 'scanFace']);
