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
        Schema::create('total_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_ticket_details', 12, 2)->default(0);
            $table->decimal('total_parking_details', 12, 2)->default(0);
            $table->decimal('total_bantuan_details', 12, 2)->default(0);
            $table->decimal('total_resto_details', 12, 2)->default(0);
            $table->decimal('total_toilet_details', 12, 2)->default(0);
            $table->decimal('total_wahana_details', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('total_incomes', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIfExists() ;
        });
        
    }
};
