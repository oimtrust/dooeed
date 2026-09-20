<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_asset_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete()->comment('kas sumber / penerima');
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete()->comment('link ke transactions');
            $table->date('transaction_date');
            $table->string('type')->comment('buy, sell');
            $table->decimal('quantity', 18, 2)->default(0);
            $table->decimal('price', 18, 2)->default(0);
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('fee_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('profit_amount', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_asset_transactions');
    }
};
