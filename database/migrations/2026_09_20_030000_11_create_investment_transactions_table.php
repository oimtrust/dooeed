<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investment_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investment_asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funding_account_id')->nullable()->constrained('accounts')->nullOnDelete()->comment('nullable untuk dividen/reinvest/opening');
            $table->date('transaction_date');
            $table->string('type')->comment('buy, sell, dividend, profit, fee');
            $table->decimal('quantity', 18, 8)->default(0);
            $table->decimal('price', 18, 2)->default(0);
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('fee_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_transactions');
    }
};
