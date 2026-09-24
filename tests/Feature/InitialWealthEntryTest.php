<?php

use App\Models\InitialWealthEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('keeps initial wealth entries private and reports an empty summary', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    InitialWealthEntry::factory()->create(['user_id' => $otherUser->id]);

    $this->withToken(jwtFor($user))->getJson('/api/v1/initial-wealth-entries')
        ->assertOk()->assertJsonCount(0, 'data')
        ->assertJsonPath('summary.assets', '0.00')->assertJsonPath('summary.liabilities', '0.00')->assertJsonPath('summary.net_worth', '0.00');
});

it('creates entries, calculates asset totals, and calculates wealth summary', function (): void {
    $user = User::factory()->create();
    $token = jwtFor($user);
    $this->withToken($token)->postJson('/api/v1/initial-wealth-entries', ['category' => 'cash', 'name' => 'BCA', 'amount' => 5_000_000])->assertCreated();
    $asset = $this->withToken($token)->postJson('/api/v1/initial-wealth-entries', ['category' => 'cash_equivalent', 'name' => 'Mutual Fund A', 'unit_price' => 100_000, 'quantity' => 1.5])
        ->assertCreated()->assertJsonPath('data.amount', '150000.00')->assertJsonPath('summary.assets', '5150000.00');
    $this->withToken($token)->postJson('/api/v1/initial-wealth-entries', ['category' => 'debt', 'name' => 'Credit Card Bank A', 'amount' => 3_000_000, 'debt_type' => 'credit_card_paylater'])
        ->assertCreated()->assertJsonPath('summary.liabilities', '3000000.00')->assertJsonPath('summary.net_worth', '2150000.00');

    $this->withToken($token)->putJson('/api/v1/initial-wealth-entries/'.$asset->json('data.id'), ['category' => 'cash_equivalent', 'name' => 'Mutual Fund A', 'unit_price' => 250_000, 'quantity' => 4])
        ->assertOk()->assertJsonPath('data.amount', '1000000.00');
});

it('validates entry fields and prevents access to another users entry', function (): void {
    $user = User::factory()->create();
    $otherEntry = InitialWealthEntry::factory()->create();
    $token = jwtFor($user);

    $this->withToken($token)->postJson('/api/v1/initial-wealth-entries', ['category' => 'non_current_asset', 'name' => '', 'unit_price' => -1, 'quantity' => 0])
        ->assertUnprocessable()->assertJsonValidationErrors(['name', 'unit_price', 'quantity']);
    $this->withToken($token)->postJson('/api/v1/initial-wealth-entries', ['category' => 'debt', 'name' => 'Loan', 'amount' => 1])
        ->assertUnprocessable()->assertJsonValidationErrors(['debt_type']);
    $this->withToken($token)->deleteJson('/api/v1/initial-wealth-entries/'.$otherEntry->id)->assertNotFound();
});
