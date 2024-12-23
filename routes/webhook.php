<?php

use App\Enums\DateType;
use App\Models\Agenda;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/google/oauth', function (GoogleCalendarService $service) {
    // Redirect user to Google OAuth
    return redirect()->away($service->getAuthUrl());
})->name('oauth');

Route::get('/webhook/callback', function (Request $request, GoogleCalendarService $service) {
    // Handle the OAuth callback and store tokens
    $service->handleAuthCallback($request->get('code'));

    return response(__('Token successfully stored!'), 200);
})->name('oauth.callback');

Route::get('/events', function (Request $request, GoogleCalendarService $service) {
    $calendarId = 'primary'; // Use 'primary' for the default calendar

    // Optionally specify a time range
    $timeMin = $request->query('timeMin', now()->toIso8601String());
    $timeMax = $request->query('timeMax', null); // No upper limit by default

    try {
        $events = $service->listEvents($calendarId, $timeMin, $timeMax);
        return response()->json($events);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
});

Route::get('/events/create', function () {
    $agenda = new Agenda();
    $agenda->summary = 'Test Event';
    $agenda->in_agenda = true;
    $agenda->type = DateType::OTHER;
    $agenda->description = 'This is a test event';
    $agenda->start = now()->addHour();
    $agenda->end = now()->addHour(2);

    $agenda->save();

    $agenda->refresh();

    return response()->json($agenda);
});
