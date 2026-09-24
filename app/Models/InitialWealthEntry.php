<?php

namespace App\Models;

use Database\Factories\InitialWealthEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'category', 'debt_type', 'name', 'amount', 'unit_price', 'quantity'])]
class InitialWealthEntry extends Model
{
    /** @use HasFactory<InitialWealthEntryFactory> */
    use HasFactory, HasUuids;

    public const Cash = 'cash';

    public const CashEquivalent = 'cash_equivalent';

    public const NonCurrentAsset = 'non_current_asset';

    public const Receivable = 'receivable';

    public const Debt = 'debt';

    /** @return array<int, string> */
    public static function categories(): array
    {
        return [self::Cash, self::CashEquivalent, self::NonCurrentAsset, self::Receivable, self::Debt];
    }

    public static function isAssetCategory(string $category): bool
    {
        return in_array($category, [self::CashEquivalent, self::NonCurrentAsset], true);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'unit_price' => 'decimal:2', 'quantity' => 'decimal:4'];
    }
}
