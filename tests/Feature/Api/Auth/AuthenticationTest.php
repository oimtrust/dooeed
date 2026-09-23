<?php

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

it('registers a user, sends an OTP, and does not issue a token before verification', function (): void {
    Notification::fake();

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Finesse',
        'email' => 'finesse@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'OTP sent')
        ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email']]]);

    $userId = $response->json('data.user.id');

    expect($userId)->toBeString();
    expect(Str::isUuid($userId))->toBeTrue();
    $this->assertDatabaseHas('users', ['id' => $userId, 'email' => 'finesse@example.com']);
    expect(User::query()->findOrFail($userId)->email_verified_at)->toBeNull();

    Notification::assertSentTo(User::query()->findOrFail($userId), EmailOtpNotification::class);
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('rejects registration with duplicate email', function (): void {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->postJson('/api/v1/auth/register', [
        'name' => 'Finesse',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('logs in with valid credentials and returns a bearer token', function (): void {
    User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->postJson('/api/v1/auth/login', [
        'email' => 'login@example.com',
        'password' => 'password123',
    ])->assertOk()
        ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email'], 'token', 'token_type']]);
});

it('rejects login with invalid credentials', function (): void {
    User::factory()->create(['email' => 'login@example.com']);

    $this->postJson('/api/v1/auth/login', [
        'email' => 'login@example.com',
        'password' => 'wrong-password',
    ])->assertUnprocessable();
});

it('returns the authenticated user', function (): void {
    $user = User::factory()->create();

    $this->withToken(jwtFor($user))
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});

it('logs out and revokes the token', function (): void {
    $user = User::factory()->create();
    $token = jwtFor($user);

    $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

    $this->withToken($token)->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('rejects unauthenticated access to me', function (): void {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('authenticates a subsequent request with the bearer token issued at login', function (): void {
    $user = User::factory()->create(['password' => 'password123']);

    expect(Str::isUuid($user->id))->toBeTrue();

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertOk()->assertJsonPath('data.user.id', $user->id);

    $this->app['auth']->forgetGuards();

    $this->withToken($response->json('data.token'))
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

it('rejects an expired bearer token', function (): void {
    $user = User::factory()->create();

    $token = jwtFor($user, 1);

    $this->travel(2)->minutes();

    $this->withToken($token)->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('rejects a malformed bearer token', function (): void {
    $this->withToken('not-a-jwt')
        ->getJson('/api/v1/auth/me')
        ->assertUnauthorized();
});

it('refreshes the bearer token and revokes the previous one', function (): void {
    $user = User::factory()->create();
    $token = jwtFor($user);

    $refreshed = $this->withToken($token)
        ->postJson('/api/v1/auth/refresh')
        ->assertOk()
        ->assertJsonStructure(['data' => ['token', 'token_type']])
        ->json('data.token');

    expect($refreshed)->not->toBe($token);

    $this->withToken($refreshed)
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);

    resetAuthState();

    $this->withToken($token)->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('rejects a refresh without a bearer token', function (): void {
    $this->postJson('/api/v1/auth/refresh')->assertUnauthorized();
});
