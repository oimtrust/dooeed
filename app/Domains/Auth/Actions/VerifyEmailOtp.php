<?php

namespace App\Domains\Auth\Actions;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VerifyEmailOtp
{
    public function execute(string $email, string $code): User
    {
        $error = DB::transaction(function () use ($email, $code): ?string {
            $user = User::query()->where('email', $email)->lockForUpdate()->first();
            $otp = $user ? EmailOtp::query()->where('user_id', $user->id)->latest('id')->lockForUpdate()->first() : null;

            if (! $otp) {
                return 'Invalid OTP';
            }

            if ($otp->used_at) {
                return 'OTP already used';
            }

            if ($otp->expires_at->isPast()) {
                return 'OTP expired';
            }

            if (! Hash::check($code, $otp->token)) {
                $otp->increment('attempts');

                if ($otp->attempts >= 5) {
                    $otp->update(['used_at' => now()]);
                }

                return 'Invalid OTP';
            }

            $otp->update(['used_at' => now()]);
            $user->forceFill(['email_verified_at' => now()])->save();

            return null;
        });

        if ($error) {
            $this->fail($error);
        }

        return User::query()->where('email', $email)->sole();
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['code' => [$message]]);
    }
}
