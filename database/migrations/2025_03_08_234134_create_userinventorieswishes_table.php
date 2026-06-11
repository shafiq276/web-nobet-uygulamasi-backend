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
        Schema::create('userinventorieswishes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('adminID');
            $table->uuid('userID');
            $table->integer('whichShift');
            $table->string('Reason');
            $table->string('ResponseReason');
            $table->enum('status', ['approved','rejected','pending']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userinventorieswishes');
    }
};
