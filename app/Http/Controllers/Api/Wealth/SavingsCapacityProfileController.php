<?php

namespace App\Http\Controllers\Api\Wealth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wealth\UpsertSavingsCapacityProfileRequest;
use App\Models\SavingsCapacityProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavingsCapacityProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->profileData(SavingsCapacityProfile::query()->whereBelongsTo($request->user())->first())]);
    }

    /** @return array<string, mixed>|null */
    private function profileData(?SavingsCapacityProfile $profile): ?array
    {
        if (! $profile) {
            return null;
        }

        return [...$profile->only(['id', 'user_id', 'monthly_income', 'monthly_expenses', 'retirement_age', 'inheritance_age']), 'date_of_birth' => $profile->date_of_birth->toDateString()];
    }

    public function store(UpsertSavingsCapacityProfileRequest $request): JsonResponse
    {
        $profile = SavingsCapacityProfile::query()->updateOrCreate(['user_id' => $request->user()->id], $request->validated());

        return response()->json(['data' => $this->profileData($profile)], 200);
    }
}
