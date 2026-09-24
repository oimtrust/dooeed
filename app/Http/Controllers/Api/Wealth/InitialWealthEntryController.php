<?php

namespace App\Http\Controllers\Api\Wealth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wealth\StoreInitialWealthEntryRequest;
use App\Http\Requests\Wealth\UpdateInitialWealthEntryRequest;
use App\Http\Resources\Wealth\InitialWealthEntryResource;
use App\Models\InitialWealthEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InitialWealthEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entries = InitialWealthEntry::query()->where('user_id', $request->user()->id)->latest()->get();

        return response()->json(['data' => InitialWealthEntryResource::collection($entries)->resolve(), 'summary' => $this->summary($request)]);
    }

    public function store(StoreInitialWealthEntryRequest $request): JsonResponse
    {
        $entry = InitialWealthEntry::create(['user_id' => $request->user()->id, ...$this->entryAttributes($request->validated())]);

        return response()->json(['data' => new InitialWealthEntryResource($entry), 'summary' => $this->summary($request)], 201);
    }

    public function update(UpdateInitialWealthEntryRequest $request, string $entry): JsonResponse
    {
        $initialWealthEntry = $this->entryFor($request, $entry);
        $initialWealthEntry->update($this->entryAttributes($request->validated()));

        return response()->json(['data' => new InitialWealthEntryResource($initialWealthEntry->fresh()), 'summary' => $this->summary($request)]);
    }

    public function destroy(Request $request, string $entry): JsonResponse
    {
        $this->entryFor($request, $entry)->delete();

        return response()->json(['summary' => $this->summary($request)]);
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function entryAttributes(array $data): array
    {
        if (InitialWealthEntry::isAssetCategory($data['category'])) {
            $data['amount'] = number_format((float) $data['unit_price'] * (float) $data['quantity'], 2, '.', '');
        }
        if ($data['category'] !== InitialWealthEntry::Debt) {
            $data['debt_type'] = null;
        }
        if (! InitialWealthEntry::isAssetCategory($data['category'])) {
            $data['unit_price'] = null;
            $data['quantity'] = null;
        }

        return $data;
    }

    private function entryFor(Request $request, string $entry): InitialWealthEntry
    {
        return InitialWealthEntry::query()->where('user_id', $request->user()->id)->findOrFail($entry);
    }

    /** @return array{assets: string, liabilities: string, net_worth: string, categories: array<string, string>} */
    private function summary(Request $request): array
    {
        $totals = InitialWealthEntry::query()->where('user_id', $request->user()->id)->select('category', DB::raw('SUM(amount) as total'))->groupBy('category')->pluck('total', 'category');
        $categories = collect(InitialWealthEntry::categories())->mapWithKeys(fn (string $category): array => [$category => number_format((float) ($totals[$category] ?? 0), 2, '.', '')])->all();
        $assets = $categories[InitialWealthEntry::Cash] + $categories[InitialWealthEntry::CashEquivalent] + $categories[InitialWealthEntry::NonCurrentAsset] + $categories[InitialWealthEntry::Receivable];
        $liabilities = $categories[InitialWealthEntry::Debt];

        return ['assets' => number_format($assets, 2, '.', ''), 'liabilities' => number_format($liabilities, 2, '.', ''), 'net_worth' => number_format($assets - $liabilities, 2, '.', ''), 'categories' => $categories];
    }
}
