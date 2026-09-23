<?php

use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\Channels\MailtrapChannel;
use App\Notifications\EmailOtpNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(LazilyRefreshDatabase::class);

it('uses the Mailtrap API channel when an API token is configured', function (): void {
    config()->set('services.mailtrap.api_token', 'mailtrap-test-token');

    $notification = new EmailOtpNotification('123456');

    expect($notification->via(User::factory()->make()))->toBe([MailtrapChannel::class]);
});

it('requires a sandbox inbox ID for the Mailtrap sandbox API', function (): void {
    config()->set('services.mailtrap.api_token', 'mailtrap-test-token');
    config()->set('services.mailtrap.mode', 'sandbox');
    config()->set('services.mailtrap.sandbox_inbox_id', null);

    app(MailtrapChannel::class)->send(User::factory()->make(), new EmailOtpNotification('123456'));
})->throws(LogicException::class, 'MAILTRAP_SANDBOX_INBOX_ID is required when MAILTRAP_API_MODE is sandbox.');

it('creates a hashed six digit OTP and emails an unverified user', function (): void {
    Notification::fake();
    $user = User::factory()->unverified()->create(['email' => 'budi@app.test']);

    $this->postJson('/api/email/otp-request', ['email' => $user->email])
        ->assertOk()
        ->assertJsonPath('message', 'OTP sent');

    $otp = EmailOtp::query()->sole();

    expect($otp->token)->not->toMatch('/^\d{6}$/')
        ->and($otp->expires_at)->toBeBetween(now()->addMinutes(9), now()->addMinutes(10)->addSecond())
        ->and($otp->used_at)->toBeNull();

    Notification::assertSentTo($user, EmailOtpNotification::class, function (EmailOtpNotification $notification) use ($otp): bool {
        expect($notification->code)->toMatch('/^\d{6}$/')
            ->and(Hash::check($notification->code, $otp->token))->toBeTrue();

        return true;
    });
});

it('verifies a valid OTP once', function (): void {
    Notification::fake();
    $user = User::factory()->unverified()->create(['email' => 'valid@app.test']);
    $this->postJson('/api/email/otp-request', ['email' => $user->email]);

    $notification = Notification::sent($user, EmailOtpNotification::class)->sole();

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])
        ->assertForbidden()
        ->assertJsonPath('message', 'Email address is not verified.');

    $this->postJson('/api/email/otp-verify', ['email' => $user->email, 'code' => $notification->code])
        ->assertOk()
        ->assertJsonPath('message', 'Email verified')
        ->assertJsonStructure(['data' => ['user' => ['id', 'email'], 'token', 'token_type']]);

    expect($user->fresh()->email_verified_at)->not->toBeNull()
        ->and(EmailOtp::query()->sole()->used_at)->not->toBeNull();

    $this->postJson('/api/email/otp-verify', ['email' => $user->email, 'code' => $notification->code])
        ->assertUnprocessable()
        ->assertJsonPath('errors.code.0', 'OTP already used');
});

it('signs a browser session in after successful OTP verification', function (): void {
    Notification::fake();
    $user = User::factory()->unverified()->create(['email' => 'browser-verify@app.test']);

    $this->postJson('/session/email/otp-request', ['email' => $user->email])->assertOk();
    $notification = Notification::sent($user, EmailOtpNotification::class)->sole();

    $this->postJson('/session/email/otp-verify', ['email' => $user->email, 'code' => $notification->code])
        ->assertOk()
        ->assertJsonPath('message', 'Email verified');

    $this->assertAuthenticatedAs($user, 'web');
});

it('increments attempts and rejects an incorrect OTP', function (): void {
    $user = User::factory()->unverified()->create(['email' => 'wrong@app.test']);
    EmailOtp::create(['user_id' => $user->id, 'token' => Hash::make('123456'), 'expires_at' => now()->addMinutes(10)]);

    $this->postJson('/api/email/otp-verify', ['email' => $user->email, 'code' => '000000'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.code.0', 'Invalid OTP');

    expect(EmailOtp::query()->sole()->attempts)->toBe(1);
});

it('rejects expired OTPs', function (): void {
    $user = User::factory()->unverified()->create(['email' => 'expired@app.test']);
    EmailOtp::create(['user_id' => $user->id, 'token' => Hash::make('123456'), 'expires_at' => now()->subSecond()]);

    $this->postJson('/api/email/otp-verify', ['email' => $user->email, 'code' => '123456'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.code.0', 'OTP expired');
});

it('invalidates the OTP after five incorrect attempts', function (): void {
    $user = User::factory()->unverified()->create(['email' => 'attempts@app.test']);
    EmailOtp::create(['user_id' => $user->id, 'token' => Hash::make('123456'), 'expires_at' => now()->addMinutes(10)]);

    foreach (range(1, 5) as $attempt) {
        $this->postJson('/api/email/otp-verify', ['email' => $user->email, 'code' => '000000'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.code.0', 'Invalid OTP');
    }

    $otp = EmailOtp::query()->sole();
    expect($otp->attempts)->toBe(5)->and($otp->used_at)->not->toBeNull();

    $this->postJson('/api/email/otp-verify', ['email' => $user->email, 'code' => '123456'])
        ->assertUnprocessable()
        ->assertJsonPath('errors.code.0', 'OTP already used');
});

it('limits resends and only keeps the latest OTP valid', function (): void {
    Notification::fake();
    $user = User::factory()->unverified()->create(['email' => 'resend@app.test']);

    foreach (range(1, 3) as $request) {
        $this->postJson('/api/email/otp-request', ['email' => $user->email])->assertOk();
    }

    $this->postJson('/api/email/otp-request', ['email' => $user->email])
        ->assertTooManyRequests()
        ->assertJsonPath('message', 'Too many requests, try again later');
    expect(EmailOtp::query()->count())->toBe(3)
        ->and(EmailOtp::query()->whereNull('used_at')->count())->toBe(1);
});

it('does not disclose unregistered addresses or send an OTP email', function (): void {
    Notification::fake();

    $this->postJson('/api/email/otp-request', ['email' => 'unknown@app.test'])
        ->assertOk()
        ->assertJsonPath('message', 'If email exists, OTP sent');

    expect(EmailOtp::query()->count())->toBe(0);
    Notification::assertNothingSent();
});
