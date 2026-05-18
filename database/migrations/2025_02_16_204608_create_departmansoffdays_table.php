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
        Schema::create('departmansoffdays', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('adminID');
            $table->uuid('departmanID');
            $table->string('offDayofweek')->nullable();
            $table->date('offdayStart')->nullable();
            $table->date('offdayEnd')->nullable();
            $table->timestamps();

            $table->foreign('departmanID')->references('id')->on('departmans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departmansoffdays');
    }
};
