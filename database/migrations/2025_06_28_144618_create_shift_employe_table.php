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
        Schema::create('shift_employe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_shift_id')
            ->constrained('list_shifts')
            ->onDelete('cascade');
            $table->foreignId('employe_id')
            ->constrained('employes')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_employe');
    }
};
