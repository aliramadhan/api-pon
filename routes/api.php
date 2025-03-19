<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiPonController;

Route::get('/api-pon', [ApiPonController::class, 'getPon']);