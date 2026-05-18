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
        Schema::create('usertotalshiftcounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('adminID');
            $table->uuid('userID');
            $table->uuid('callenderID');
            $table->integer('totalShiftCount');
            $table->timestamps();

            $table->foreign('callenderID')->references('id')->on('pastcallenders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usertotalshiftcounts');
    }
};
