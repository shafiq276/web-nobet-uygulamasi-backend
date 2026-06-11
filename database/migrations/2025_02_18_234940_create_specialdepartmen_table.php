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
        
        Schema::create('specialdepartmen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('adminID');
            $table->uuid('userID');
            $table->string('departmanName');
            $table->uuid('departmanID');
            $table->timestamps();

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialdepertman');
    }
};
