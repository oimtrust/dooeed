<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts')->restrictOnDelete();
            $table->date('transfer_date');
            $table->decimal('amount', 18, 2);
            $table->decimal('fee_amount', 18, 2)->default(0);
            $table->foreignId('fee_transaction_id')->nullable()->constrained('transactions')->nullOnDelete()->comment('opsional expense fee');
            $table->text('description')->nullable();
            $table->foreignId('transfer_out_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('transfer_in_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
