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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('provider'); // 1 = local, 2 = google
            $table->text('self_introduction');
            $table->string('avatar_file_path');
            $table->boolean('is_pro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('provider');
            $table->dropColumn('self_introduction');
            $table->dropColumn('avatar_file_path');
            $table->dropColumn('is_pro');
        });
    }
};
