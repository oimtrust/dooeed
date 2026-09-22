<?php

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->target = User::factory()->create(['name' => 'Budi', 'email' => 'budi@app.test']);
});

it('requires authentication and forbids ordinary users throughout the admin panel', function (): void {
    $this->get('/admin/users')->assertRedirect(route('login'));
    $this->actingAs($this->target)->get('/admin/users')->assertForbidden()->assertSee('http-equiv="refresh"', false);
    $this->get('/admin/users/'.$this->admin->id)->assertForbidden();
    $this->get('/admin/audit-logs')->assertForbidden();
    $this->patchJson('/api/v1/admin/users/'.$this->admin->id, ['action' => 'suspend', 'reason' => 'spam', 'confirmed' => 1])->assertForbidden();
    $this->postJson('/admin/session')->assertForbidden();
    expect(AuditLog::count())->toBe(0);
});

it('bridges bearer authentication to an admin web session', function (): void {
    $token = $this->admin->createToken('test')->plainTextToken;
    $this->withToken($token)->postJson('/admin/session')->assertOk()->assertJsonPath('redirect', route('admin.users.index'));
    $this->assertAuthenticatedAs($this->admin, 'web');
    $this->get('/admin/users')->assertOk();
    $this->deleteJson('/admin/session')->assertOk();
    $this->assertGuest('web');
});

it('lists users with counts, validated sorting, combined filters and twenty per page', function (): void {
    User::factory()->count(22)->create();
    User::factory()->create(['name' => 'Budi suspended', 'status' => 'suspended']);
    $this->actingAs($this->admin)->getJson('/api/v1/admin/users?sort=name&direction=asc')->assertOk()
        ->assertJsonCount(20, 'data')->assertJsonPath('meta.per_page', 20)->assertJsonPath('meta.total', 25)
        ->assertJsonStructure(['data' => [['id', 'name', 'email', 'role', 'status', 'accounts_count', 'transactions_count', 'created_at']], 'links', 'meta']);
    $this->getJson('/api/v1/admin/users?search=Budi&status=active')->assertOk()
        ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $this->target->id);
    $this->getJson('/api/v1/admin/users?search=budi@app.test')->assertOk()->assertSee('budi@app.test');
    $this->getJson('/api/v1/admin/users?sort=invalid')->assertUnprocessable();
    $this->getJson('/api/v1/admin/users?page=2')->assertOk()->assertJsonCount(5, 'data');
});

it('suspends and activates a user, revokes tokens and records the reason', function (): void {
    $this->target->createToken('old');
    $this->actingAs($this->admin)->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'suspend', 'reason' => 'spam', 'confirmed' => 1])->assertOk();
    expect($this->target->fresh()->status)->toBe('suspended');
    expect($this->target->tokens()->count())->toBe(0);
    $this->assertDatabaseHas('audit_logs', ['admin_id' => $this->admin->id, 'target_user_id' => $this->target->id, 'action' => 'suspend', 'reason' => 'spam']);
    $this->postJson('/api/v1/auth/login', ['email' => $this->target->email, 'password' => 'password'])
        ->assertUnprocessable()->assertJsonPath('errors.email.0', 'Akun ditangguhkan');
    $this->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'activate', 'confirmed' => 1])->assertOk();
    expect($this->target->fresh()->status)->toBe('active');
    $this->postJson('/api/v1/auth/login', ['email' => $this->target->email, 'password' => 'password'])->assertOk();
});

it('requires confirmation and a suspension reason and protects the acting admin', function (): void {
    $this->actingAs($this->admin)->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'suspend'])->assertUnprocessable()->assertJsonValidationErrors(['reason', 'confirmed']);
    $this->patchJson('/api/v1/admin/users/'.$this->admin->id, ['action' => 'suspend', 'reason' => 'spam', 'confirmed' => 1])->assertUnprocessable();
    $this->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'change_role', 'role' => 'owner', 'confirmed' => 1])->assertUnprocessable();
    expect(AuditLog::count())->toBe(0);
});

it('resets passwords immediately and sends a working reset link', function (): void {
    Notification::fake();
    $this->target->createToken('old');
    $this->actingAs($this->admin)->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'reset_password', 'confirmed' => 1])->assertOk();
    expect(Hash::check('password', $this->target->fresh()->password))->toBeFalse();
    expect($this->target->tokens()->count())->toBe(0);
    $this->postJson('/api/v1/auth/login', ['email' => $this->target->email, 'password' => 'password'])->assertUnprocessable();
    Notification::assertSentTo($this->target, ResetPassword::class, function ($notification): bool {
        $this->postJson('/api/v1/auth/reset-password', ['email' => $this->target->email, 'token' => $notification->token, 'password' => 'new-password123', 'password_confirmation' => 'new-password123'])->assertOk();

        return true;
    });
    $this->assertDatabaseHas('audit_logs', ['action' => 'reset_password', 'target_user_id' => $this->target->id]);
});

it('changes roles and only soft deletes suspended users while preserving audit history', function (): void {
    $this->actingAs($this->admin)->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'delete', 'confirmed' => 1])->assertUnprocessable();
    $this->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'change_role', 'role' => 'admin', 'confirmed' => 1])->assertOk();
    expect($this->target->fresh()->role)->toBe('admin');
    $this->getJson('/api/v1/admin/users?search=budi')->assertJsonPath('data.0.role', 'admin');
    $this->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'suspend', 'reason' => 'spam', 'confirmed' => 1]);
    $this->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'delete', 'confirmed' => 1])->assertOk();
    $this->assertSoftDeleted($this->target);
    $this->getJson('/api/v1/admin/users')->assertDontSee($this->target->email);
    $this->getJson('/api/v1/admin/users/'.$this->target->id)->assertNotFound();
    $this->getJson('/api/v1/admin/audit-logs?admin_id='.$this->admin->id.'&target_user_id='.$this->target->id)->assertOk()->assertSee($this->target->email)
        ->assertJsonPath('meta.total', 3);
    $this->getJson('/api/v1/admin/audit-logs?admin_id='.$this->target->id)->assertJsonCount(0, 'data');
});

it('rejects suspended admins and suspended bearer users', function (): void {
    $this->admin->forceFill(['status' => 'suspended'])->save();
    $this->actingAs($this->admin)->get('/admin/users')->assertForbidden();
    $this->getJson('/api/v1/auth/me')->assertForbidden();
});

it('does not allow registration to assign an admin role or status', function (): void {
    $this->postJson('/api/v1/auth/register', ['name' => 'New', 'email' => 'new@app.test', 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 'admin', 'status' => 'suspended'])->assertCreated();
    expect(User::where('email', 'new@app.test')->first())->role->toBe('user')->status->toBe('active');
});

it('shows only the selected users financial summary and latest twenty transactions', function (): void {
    $type = (string) Str::uuid();
    DB::table('account_types')->insert(['id' => $type, 'name' => 'Bank']);
    $account = (string) Str::uuid();
    DB::table('accounts')->insert(['id' => $account, 'user_id' => $this->target->id, 'account_type_id' => $type, 'name' => 'BCA', 'current_balance' => 125000, 'currency' => 'IDR']);
    foreach (range(1, 21) as $day) {
        DB::table('transactions')->insert(['id' => (string) Str::uuid(), 'user_id' => $this->target->id, 'account_id' => $account, 'transaction_date' => '2026-09-'.str_pad((string) $day, 2, '0', STR_PAD_LEFT), 'type' => 'income', 'amount' => 100, 'description' => 'transaction-'.$day]);
    }
    $contact = (string) Str::uuid();
    DB::table('contacts')->insert(['id' => $contact, 'user_id' => $this->target->id, 'name' => 'Lender', 'type' => 'person']);
    foreach ([['debt', 'active', 500], ['debt', 'overdue', 200], ['receivable', 'active', 900], ['debt', 'canceled', 1000]] as [$type, $status, $amount]) {
        DB::table('debts')->insert(['id' => (string) Str::uuid(), 'user_id' => $this->target->id, 'contact_id' => $contact, 'title' => 'Debt', 'type' => $type, 'status' => $status, 'principal_amount' => $amount, 'outstanding_amount' => $amount, 'start_date' => '2026-09-01']);
    }
    $investment = (string) Str::uuid();
    DB::table('investment_accounts')->insert(['id' => $investment, 'user_id' => $this->target->id, 'name' => 'Portfolio']);
    DB::table('investment_assets')->insert(['id' => (string) Str::uuid(), 'investment_account_id' => $investment, 'asset_code' => 'TEST', 'asset_name' => 'Test asset', 'asset_type' => 'stock']);
    $this->actingAs($this->admin)->getJson('/api/v1/admin/users/'.$this->target->id)->assertOk()->assertJsonPath('data.balances.0.total', '125000')
        ->assertJsonPath('data.outstanding_debts', '700')
        ->assertJsonCount(1, 'data.investments')->assertJsonPath('data.investments.0.assets_count', 1)
        ->assertJsonCount(20, 'data.transactions')->assertJsonPath('data.transactions.0.description', 'transaction-21')->assertJsonPath('data.transactions.19.description', 'transaction-2')
        ->assertJsonCount(1, 'data.accounts');
    $this->getJson('/api/v1/admin/users/'.$this->admin->id)->assertOk()->assertJsonCount(0, 'data.transactions');
    $this->getJson('/api/v1/admin/users?search=budi')->assertJsonPath('data.0.accounts_count', 1)->assertJsonPath('data.0.transactions_count', 21);
});

it('creates a browser login session and returns 403 to non-admin direct navigation', function (): void {
    $this->postJson('/session/login', ['email' => $this->target->email, 'password' => 'password'])->assertOk();
    $this->assertAuthenticatedAs($this->target, 'web');
    $this->get('/admin/users')->assertForbidden();
});

it('invalidates existing admin sessions after a credential reset', function (): void {
    Notification::fake();
    $otherAdmin = User::factory()->create(['role' => 'admin']);
    $oldPasswordHash = $otherAdmin->password;
    $this->actingAs($this->admin)->patchJson('/api/v1/admin/users/'.$otherAdmin->id, ['action' => 'reset_password', 'confirmed' => 1])->assertOk();
    $this->actingAs($otherAdmin->fresh(), 'web')->withSession(['password_hash_web' => $oldPasswordHash])->get('/admin/users')->assertRedirect(route('login'));
});

it('rolls back password changes and audit when reset delivery is throttled', function (): void {
    Notification::fake();
    Password::createToken($this->target);
    $oldPassword = $this->target->password;
    $this->target->createToken('retained');
    $this->actingAs($this->admin)->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'reset_password', 'confirmed' => 1])->assertUnprocessable();
    expect($this->target->fresh()->password)->toBe($oldPassword);
    expect($this->target->tokens()->count())->toBe(1);
    expect(AuditLog::count())->toBe(0);
});

it('keeps browser registration authenticated without granting admin privileges', function (): void {
    $this->postJson('/session/register', ['name' => 'New browser user', 'email' => 'browser@app.test', 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 'admin'])->assertCreated();
    $this->assertAuthenticated('web');
    $this->get('/admin/users')->assertForbidden();
});

it('serves view shells without embedding user records or accepting web mutations', function (): void {
    $this->actingAs($this->admin)->get('/admin/users')->assertOk()->assertSee('data-page="admin-users"', false)->assertDontSee($this->target->email);
    $this->get('/admin/users/'.$this->target->id)->assertOk()->assertSee('data-page="admin-user-detail"', false)->assertDontSee($this->target->email);
    $this->get('/admin/audit-logs')->assertOk()->assertSee('data-page="admin-audit"', false);
    $this->patch('/admin/users/'.$this->target->id, ['action' => 'suspend', 'confirmed' => true, 'reason' => 'spam'])->assertStatus(405);
    expect($this->target->fresh()->status)->toBe('active');
});

it('protects all admin API endpoints independently of the HTML shell', function (): void {
    $this->getJson('/api/v1/admin/users')->assertUnauthorized();
    $this->getJson('/api/v1/admin/users/'.$this->target->id)->assertUnauthorized();
    $this->getJson('/api/v1/admin/audit-logs')->assertUnauthorized();
    $this->patchJson('/api/v1/admin/users/'.$this->target->id, [])->assertUnauthorized();
    $this->actingAs($this->target)->getJson('/api/v1/admin/users')->assertForbidden();
    $this->getJson('/api/v1/admin/users/'.$this->admin->id)->assertForbidden();
    $this->getJson('/api/v1/admin/audit-logs')->assertForbidden();
});

it('supports bearer-only admin API clients and never exposes credentials', function (): void {
    $token = $this->admin->createToken('api-client')->plainTextToken;
    $this->withToken($token)->getJson('/api/v1/admin/users?search=budi')->assertOk()
        ->assertJsonMissingPath('data.0.password')->assertJsonMissingPath('data.0.remember_token');
    $this->getJson('/api/v1/admin/users/'.$this->target->id)->assertOk()
        ->assertJsonPath('data.user.id', $this->target->id)->assertJsonMissingPath('data.user.password');
    $this->patchJson('/api/v1/admin/users/'.$this->target->id, ['action' => 'suspend', 'reason' => 'spam', 'confirmed' => true])->assertOk()->assertJsonStructure(['message']);
    $this->assertDatabaseHas('audit_logs', ['action' => 'suspend', 'target_user_id' => $this->target->id]);
});

it('returns JSON API errors even without an Accept header', function (): void {
    $this->get('/api/v1/admin/users')->assertUnauthorized()->assertHeader('Content-Type', 'application/json');
    $this->actingAs($this->target)->get('/api/v1/admin/users')->assertForbidden()->assertHeader('Content-Type', 'application/json');
    $this->actingAs($this->admin)->get('/api/v1/admin/users?sort=invalid')->assertUnprocessable()->assertJsonValidationErrors('sort');
});
