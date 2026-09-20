<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete()->comment('nullable untuk saldo awal KPR');
            $table->foreignId('contact_id')->constrained()->restrictOnDelete();
            $table->string('kind')->default('loan')->comment('loan, credit_card, paylater');
            $table->string('type')->comment('debt = utang, receivable = piutang');
            $table->string('title');
            $table->decimal('principal_amount', 18, 2);
            $table->decimal('outstanding_amount', 18, 2);
            $table->date('start_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('active')->comment('active, paid, overdue, canceled');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
