<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

Route::post('/validate', [FileUploadController::class, 'validate']);

Route::post('/upload', [FileUploadController::class, 'upload']);