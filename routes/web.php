<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require base_path('routes/webhook.php');

Route::get('/', function () {
    return view('welcome');
});


