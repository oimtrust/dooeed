<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('account_type_id')->constrained()->restrictOnDelete();
            $table->string('name')->comment('BCA, Jago, Blu + amplop: Rumah, Education, Dana Darurat');
            $table->string('account_number')->nullable();
            $table->string('currency')->default('IDR');
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('current_balance', 18, 2)->default(0)->comment('maintain via observer, jangan input manual');
            $table->decimal('target_amount', 18, 2)->nullable()->comment('untuk Dream Tracker, null jika bukan amplop');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
