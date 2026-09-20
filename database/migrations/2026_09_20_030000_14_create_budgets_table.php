<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->date('month')->comment('isi tgl 1 tiap bulan');
            $table->string('group_name')->comment('pendapatan, kebutuhan_pokok, beli_barang, beli_aset, bayar_utang, tabungan');
            $table->decimal('planned_pct', 5, 4)->nullable();
            $table->decimal('planned_amount', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month', 'group_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
