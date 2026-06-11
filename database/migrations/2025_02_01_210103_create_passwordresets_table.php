<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('userID');
            $table->uuid('email');
            $table->string('code'); // 6 haneli doğrulama kodu
            $table->timestamp('created_at')->useCurrent();

            
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};
