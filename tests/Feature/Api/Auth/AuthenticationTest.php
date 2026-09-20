<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

it('registers a user and returns a bearer token', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Finesse',
        'email' => 'finesse@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email'], 'token', 'token_type']]);

    $userId = $response->json('data.user.id');

    expect($userId)->toBeString();
    expect(Str::isUuid($userId))->toBeTrue();

    $this->assertDatabaseHas('users', ['id' => $userId, 'email' => 'finesse@example.com']);
    $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $userId]);

    $this->withToken($response->json('data.token'))
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.id', $userId);
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

    Sanctum::actingAs($user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});

it('logs out and revokes the token', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson('/api/v1/auth/logout')->assertOk();

    expect($user->tokens()->count())->toBe(0);
});

it('rejects unauthenticated access to me', function (): void {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});
