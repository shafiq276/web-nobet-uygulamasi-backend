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
        Schema::create('departmans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('adminID');
            $table->string('departmanName');
            $table->enum('priority', ['low','medium','urgent']);
            $table->string('RgbNumber');
            $table->tinyInteger('AuserCountsShift1');
            $table->tinyInteger('AuserCountsShift2');
            $table->tinyInteger('AuserCountsShift3');
            $table->tinyInteger('AuserCountsShift4');
            $table->tinyInteger('AuserCountsShift5');
            $table->tinyInteger('BuserCountsShift1');
            $table->tinyInteger('BuserCountsShift2');
            $table->tinyInteger('BuserCountsShift3');
            $table->tinyInteger('BuserCountsShift4');
            $table->tinyInteger('BuserCountsShift5');
            $table->tinyInteger('CuserCountsShift1');
            $table->tinyInteger('CuserCountsShift2');
            $table->tinyInteger('CuserCountsShift3');
            $table->tinyInteger('CuserCountsShift4');
            $table->tinyInteger('CuserCountsShift5');
            $table->tinyInteger('DuserCountsShift1');
            $table->tinyInteger('DuserCountsShift2');
            $table->tinyInteger('DuserCountsShift3');
            $table->tinyInteger('DuserCountsShift4');
            $table->tinyInteger('DuserCountsShift5');
            $table->tinyInteger('EuserCountsShift1');
            $table->tinyInteger('EuserCountsShift2');
            $table->tinyInteger('EuserCountsShift3');
            $table->tinyInteger('EuserCountsShift4');
            $table->tinyInteger('EuserCountsShift5');
            $table->tinyInteger('FuserCountsShift1');
            $table->tinyInteger('FuserCountsShift2');
            $table->tinyInteger('FuserCountsShift3');
            $table->tinyInteger('FuserCountsShift4');
            $table->tinyInteger('FuserCountsShift5');
            $table->tinyInteger('GuserCountsShift1');
            $table->tinyInteger('GuserCountsShift2');
            $table->tinyInteger('GuserCountsShift3');
            $table->tinyInteger('GuserCountsShift4');
            $table->tinyInteger('GuserCountsShift5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departmans');
    }
};
