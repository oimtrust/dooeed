<?php

namespace App\Domains\Admin\Actions;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ManageUser
{
    /** @param array{action: string, reason?: ?string, role?: string, confirmed: mixed} $data */
    public function execute(User $admin, User $target, array $data): void
    {
        DB::transaction(function () use ($admin, $target, $data): void {
            $user = User::query()->lockForUpdate()->findOrFail($target->id);
            $action = $data['action'];

            if ($admin->is($user)) {
                throw ValidationException::withMessages(['action' => 'Tindakan terhadap akun sendiri tidak diizinkan.']);
            }

            if ($action === 'delete' && $user->status !== 'suspended') {
                throw ValidationException::withMessages(['action' => 'Suspend user sebelum menghapus akun.']);
            }

            $before = $user->only(['role', 'status']);

            match ($action) {
                'suspend' => $user->status = 'suspended',
                'activate' => $user->status = 'active',
                'change_role' => $user->role = $data['role'],
                'reset_password' => $user->password = Str::random(64),
                default => null,
            };

            $user->setRememberToken(Str::random(60));
            $user->save();

            if (config('session.driver') === 'database') {
                DB::connection(config('session.connection'))
                    ->table(config('session.table'))->where('user_id', $user->id)->delete();
            }

            if ($action === 'delete') {
                $user->delete();
                Password::deleteToken($user);
            }

            AuditLog::create([
                'admin_id' => $admin->id,
                'target_user_id' => $user->id,
                'action' => $action,
                'reason' => $data['reason'] ?? null,
                'metadata' => ['before' => $before, 'after' => $user->only(['role', 'status'])],
            ]);

            if ($action === 'reset_password') {
                $status = Password::sendResetLink(['email' => $user->email]);

                if ($status !== Password::RESET_LINK_SENT) {
                    throw ValidationException::withMessages(['action' => __($status)]);
                }
            }
        });
    }
}
