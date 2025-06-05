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
        Schema::create('resto_income_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('income_id')->constrained('incomes')->onDelete('cascade');
            $table->string('name_customer');
            $table->string('makanan');
            $table->string('minuman');
            $table->integer('qty_makanan');
            $table->integer('qty_minuman');
            $table->decimal('harga_satuan_makanan', 12, 2);
            $table->decimal('harga_satuan_minuman', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resto_income_details');
    }
};
