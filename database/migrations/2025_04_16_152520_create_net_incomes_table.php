<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('net_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('total_income_id')->constrained()->onDelete('cascade');
            $table->decimal('total_income', 12, 2)->default(0);
            $table->decimal('total_expense', 12, 2)->default(0);
            $table->decimal('net_income', 12, 2)->storedAs('total_income - total_expense');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('net_incomes');
    }
};
