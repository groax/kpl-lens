<?php

namespace Tests\Feature;

use App\Models\GoogleToken;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Exception;
use Google_Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GoogleCalendarServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $googleClientMock;
    protected GoogleCalendarService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Google_Client
        $this->googleClientMock = Mockery::mock(Google_Client::class);

        // Inject the mock into the service
        $this->service = new GoogleCalendarService();
        $this->service->client = $this->googleClientMock;
    }

    public function test_handle_auth_callback_saves_tokens_to_database()
    {
        // Arrange: Mock the response from Google
        $authCode = 'test-auth-code';
        $tokenData = [
            'access_token' => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
            'expires_in' => 3600,
            'scope' => 'https://www.googleapis.com/auth/calendar',
            'token_type' => 'Bearer',
        ];

        $this->googleClientMock->shouldReceive('setRedirectUri')->once();
        $this->googleClientMock->shouldReceive('fetchAccessTokenWithAuthCode')
            ->with($authCode)
            ->andReturn($tokenData);

        // Act: Call the method
        $token = $this->service->handleAuthCallback($authCode);

        // Assert: Check the database and returned data
        $this->assertDatabaseHas('google_tokens', [
            'type' => 'google-calendar',
            'access_token' => 'test-access-token',
            'refresh_token' => 'test-refresh-token',
        ]);
        $this->assertEquals('test-access-token', $token->access_token);
    }

    public function test_get_valid_access_token_refreshes_if_expired()
    {
        // Arrange: Create an expired token in the database
        $token = GoogleToken::query()->create([
            'type' => 'google-calendar',
            'access_token' => 'expired-access-token',
            'refresh_token' => 'valid-refresh-token',
            'expires_in' => 3600,
            'scope' => 'https://www.googleapis.com/auth/calendar',
            'token_type' => 'Bearer',
            'created' => Carbon::now()->subHours(2),
        ]);

        $newTokenData = [
            'access_token' => 'new-access-token',
            'expires_in' => 3600,
        ];

        $this->googleClientMock->shouldReceive('refreshToken')
            ->with($token->refresh_token)
            ->once();
        $this->googleClientMock->shouldReceive('getAccessToken')
            ->andReturn($newTokenData);

        // Act: Call the method
        $accessToken = $this->service->getValidAccessToken();

        // Assert: Check that the token was refreshed
        $this->assertEquals('new-access-token', $accessToken);
        $this->assertDatabaseHas('google_tokens', [
            'type' => 'google-calendar',
            'access_token' => 'new-access-token',
        ]);
    }

    public function test_get_valid_access_token_throws_exception_if_no_token()
    {
        // Expect an exception if no token is available
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No token found. Please authenticate with Google.');

        // Act: Call the method without any tokens in the database
        $this->service->getValidAccessToken();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
