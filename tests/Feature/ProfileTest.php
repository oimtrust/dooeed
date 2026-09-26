<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(LazilyRefreshDatabase::class);

it('updates password and preferences for the authenticated user', function (): void {
    $user = User::factory()->create(['password' => 'password123']);
    $token = jwtFor($user);

    $this->withToken($token)->putJson('/api/v1/profile/preferences', ['preferred_locale' => 'en', 'preferred_currency' => 'JPY'])
        ->assertOk()->assertJsonPath('data.preferred_locale', 'en')->assertJsonPath('data.preferred_currency', 'JPY');
    $this->withToken($token)->putJson('/api/v1/profile/password', ['current_password' => 'password123', 'password' => 'new-password123', 'password_confirmation' => 'new-password123'])
        ->assertOk();

    expect(Hash::check('new-password123', $user->fresh()->password))->toBeTrue();
});
