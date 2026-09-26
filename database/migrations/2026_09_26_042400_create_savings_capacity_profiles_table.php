<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_capacity_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('monthly_income', 18, 2);
            $table->decimal('monthly_expenses', 18, 2);
            $table->date('date_of_birth');
            $table->unsignedSmallInteger('retirement_age');
            $table->unsignedSmallInteger('inheritance_age');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_capacity_profiles');
    }
};
