<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domains\Admin\Actions\ManageUser;
use App\Domains\Admin\Queries\GetUserSummary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserActionRequest;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Http\Resources\Admin\UserDetailResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(UserIndexRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $users = User::query()->select('users.*')
            ->selectSub(DB::table('accounts')->selectRaw('count(*)')->whereColumn('user_id', 'users.id'), 'accounts_count')
            ->selectSub(DB::table('transactions')->selectRaw('count(*)')->whereColumn('user_id', 'users.id'), 'transactions_count')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query
                ->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy($filters['sort'] ?? 'created_at', $filters['direction'] ?? 'desc')
            ->orderBy('id')->paginate(20)->withQueryString();

        return UserResource::collection($users);
    }

    public function show(User $user, GetUserSummary $summary): UserDetailResource
    {
        return new UserDetailResource($summary->execute($user));
    }

    public function update(UserActionRequest $request, User $user, ManageUser $manageUser): JsonResponse
    {
        $manageUser->execute($request->user(), $user, $request->validated());

        return response()->json(['message' => $request->validated('action') === 'reset_password'
            ? 'Link reset dikirim. Password dan token lama sudah dibatalkan.' : 'Tindakan berhasil disimpan.']);
    }
}
