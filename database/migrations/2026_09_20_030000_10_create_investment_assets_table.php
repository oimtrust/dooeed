<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_account_id')->constrained()->cascadeOnDelete();
            $table->string('asset_code');
            $table->string('asset_name');
            $table->string('asset_type')->comment('stock, mutual_fund, gold, crypto, bond, deposito');
            $table->decimal('opening_quantity', 18, 8)->default(0);
            $table->decimal('opening_price', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['investment_account_id', 'asset_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_assets');
    }
};
