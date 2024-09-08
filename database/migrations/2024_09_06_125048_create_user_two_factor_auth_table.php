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
        Schema::create('user_two_factor_auth', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('secret_key');
            //            $table->ulid('user_id')->index();
            $table->foreignUlid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('recovery_codes');
            $table->integer('confirmed_at')->nullable();
            $table->integer('created_at')->index()->nullable();
            $table->integer('updated_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_two_factor_auth');
    }
};
