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
        Schema::create('specialtasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('adminID');
            $table->uuid('userID');
            $table->uuid('departmanID');
            $table->string('departmanName');
            $table->string('SpecialtaskReason')->nullable();
            $table->string('ResponsSpecialtaskReason')->nullable();
            $table->enum('shiftDay', ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']);
            $table->integer('whichShift');
            $table->enum('status', ['approved','rejected','pending']);
            $table->timestamps();

            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialtasks');
    }
};
