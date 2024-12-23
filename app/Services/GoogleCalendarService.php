<?php

namespace App\Services;

use App\Traits\Google\Calendar\EventGoogleCalendarTrait;
use Exception;
use Google_Client;
use App\Models\GoogleToken;
use Illuminate\Support\Arr;

class GoogleCalendarService
{
    use EventGoogleCalendarTrait;

    public Google_Client $client;
    private GoogleToken $token;

    /**
     * @throws \Google\Exception
     */
    public function __construct()
    {
        // Initialize Google Client
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/google-calendar.json'));
        $this->client->addScope(\Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline'); // Needed for refresh tokens
    }

    /**
     * Generate the Google OAuth URL.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        $this->client->setPrompt('consent');
        $this->client->setRedirectUri(route('oauth.callback'));

        return $this->client->createAuthUrl();
    }

    /**
     * Handle the callback after user authentication and save tokens in the database.
     *
     * @param string $authCode
     * @return GoogleToken
     */
    public function handleAuthCallback(string $authCode): GoogleToken
    {
        $this->client->setRedirectUri(route('oauth.callback'));
        $tokenData = $this->client->fetchAccessTokenWithAuthCode($authCode);

        return GoogleToken::query()->updateOrCreate(
            ['type' => 'google-calendar'], // Search criteria
            [
                'access_token' => Arr::get($tokenData, 'access_token'),
                'refresh_token' => Arr::get($tokenData, 'refresh_token', null),
                'expires_in' => Arr::get($tokenData, 'expires_in'),
                'scope' => Arr::get($tokenData, 'scope'),
                'token_type' => Arr::get($tokenData, 'token_type'),
            ]
        );
    }

    /**
     * Retrieve a valid access token. Refresh if expired or fetch a new one if missing.
     *
     * @return string
     * @throws Exception
     */
    public function getValidAccessToken(): string
    {
        // Retrieve the token from the database
        $token = GoogleToken::query()->where('type', 'google-calendar')->first();

        if (!$token) {
            throw new Exception('No token found. Please authenticate with Google.');
        }

        // Check if the token is expired
        if ($token->isExpired()) {
            // Refresh the token
            $token = $this->refreshAccessToken($token);
        }

        $this->token = $token;

        return $token->access_token;
    }

    /**
     * Refresh the access token and save it in the database.
     *
     * @param GoogleToken $token
     * @return GoogleToken
     * @throws Exception
     */
    public function refreshAccessToken(GoogleToken $token): GoogleToken
    {
        if (!$token->refresh_token) {
            throw new Exception('No refresh token available. Please re-authenticate with Google.');
        }

        $this->client->refreshToken($token->refresh_token);
        $newToken = $this->client->getAccessToken();

        $token->update([
            'access_token' => Arr::get($newToken, 'access_token'),
            'expires_in' => Arr::get($newToken, 'expires_in'),
        ]);

        $this->token = $token;

        return $token;
    }
}
