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
        Schema::create('model_emails', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('model');
            $table->string('address')->index()->unique();

            $table->foreignUlid('created_by')
                ->index()
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignUlid('updated_by')
                ->index()
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignUlid('deleted_by')
                ->index()
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->integer('deleted_at')->index()->nullable();
            $table->integer('created_at')->index()->nullable();
            $table->integer('updated_at')->index()->nullable();

            $table->index('id', 'emails_id_idx', 'hash');
            $table->index('model_id', 'emails_model_id_idx', 'hash');
            $table->index('model_type', 'emails_model_type_idx', 'hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_emails');
    }
};
