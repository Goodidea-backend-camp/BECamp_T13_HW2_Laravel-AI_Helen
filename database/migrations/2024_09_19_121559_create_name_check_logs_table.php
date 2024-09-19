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
        Schema::create('name_check_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_ip_address');
            $table->string('user_name');
            $table->string('user_email');
            $table->text('message');
            $table->string('response');
            $table->boolean('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('name_check_logs');
    }
};
