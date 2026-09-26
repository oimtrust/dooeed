<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('stores and retrieves a users savings capacity profile', function (): void {
    $user = User::factory()->create();
    $payload = ['monthly_income' => 19_374_526, 'monthly_expenses' => 9_000_000, 'date_of_birth' => now()->subYears(32)->toDateString(), 'retirement_age' => 60, 'inheritance_age' => 80];
    $this->withToken(jwtFor($user))->putJson('/api/v1/savings-capacity-profile', $payload)->assertOk()->assertJsonPath('data.monthly_income', '19374526.00')->assertJsonPath('data.date_of_birth', $payload['date_of_birth']);
    $this->withToken(jwtFor($user))->getJson('/api/v1/savings-capacity-profile')->assertOk()->assertJsonPath('data.retirement_age', 60);
});

it('validates the monetary fields and retirement age sequence', function (): void {
    $user = User::factory()->create();
    $this->withToken(jwtFor($user))->putJson('/api/v1/savings-capacity-profile', ['monthly_income' => -1, 'monthly_expenses' => 'x', 'date_of_birth' => now()->subYears(32)->toDateString(), 'retirement_age' => 32, 'inheritance_age' => 32])
        ->assertUnprocessable()->assertJsonValidationErrors(['monthly_income', 'monthly_expenses', 'retirement_age', 'inheritance_age']);
});
