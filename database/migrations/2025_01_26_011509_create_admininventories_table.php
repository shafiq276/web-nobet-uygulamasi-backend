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
        Schema::create('admininventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('adminID');
            $table->boolean('balance')->default(0);
            $table->integer('numberofcalendars')->default(5);
            $table->enum('typeofadmin', ['0', '1', '2', '3'])->default('0');
            $table->datetime('tymofShoppin')->default('2000-01-01 00:00:00');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admininventories');
    }
};
