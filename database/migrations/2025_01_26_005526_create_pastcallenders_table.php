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
        Schema::create('pastcallenders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('callenderName');
            $table->string('adminID');
            $table->json('pastCallender');
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pastcallenders');
    }
};
