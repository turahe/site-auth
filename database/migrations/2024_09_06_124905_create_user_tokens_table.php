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
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary();
            //            $table->ulid('user_id')->index();
            $table->foreignUlid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->integer('last_used_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('created_at')->index()->nullable();
            $table->integer('updated_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
    }
};
