<?php

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/google/oauth', function (GoogleCalendarService $service) {
    // Redirect user to Google OAuth
    return redirect()->away($service->getAuthUrl());
})->name('oauth');

Route::get('/webhook/callback', function (Request $request, GoogleCalendarService $service) {
    // Handle the OAuth callback and store tokens
    $service->handleAuthCallback($request->code);

    return response(__('Token successfully stored!'), 200);
})->name('oauth.callback');
