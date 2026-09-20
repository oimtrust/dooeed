<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->date('transaction_date');
            $table->string('type')->comment('income, expense, transfer_in, transfer_out');
            $table->decimal('amount', 18, 2);
            $table->text('description')->nullable();
            $table->string('reference_no')->nullable();
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'transaction_date']);
            $table->index(['account_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
