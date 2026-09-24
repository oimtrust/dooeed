<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('initial_wealth_entries', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('category')->comment('cash, cash_equivalent, non_current_asset, receivable, debt');
            $table->string('debt_type')->nullable()->comment('other, credit_card_paylater; only for debt');
            $table->string('name');
            $table->decimal('amount', 18, 2)->comment('stored total value');
            $table->decimal('unit_price', 18, 2)->nullable()->comment('per unit purchase value for asset categories');
            $table->decimal('quantity', 18, 4)->nullable()->comment('asset quantity; supports fractional cash equivalents');
            $table->timestamps();

            $table->index(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('initial_wealth_entries');
    }
};
