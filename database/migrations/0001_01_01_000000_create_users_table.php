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
        Schema::create('users', function (Blueprint $table) {

            // Primary key UUID
            $table->char('id', 36)->primary();

            // Foreign keys (UUIDs)
            $table->char('franchise_id', 36);
            $table->char('business_unit_id', 36)->nullable();

            // Authentication + Profile fields
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);

            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();

            // Status flags
            $table->boolean('is_active')->default(1);
            $table->boolean('is_deleted')->default(0);

            // Audit fields
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();

            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // Login tracking
            $table->timestamp('last_login')->nullable();
            $table->integer('failed_login_attempts')->default(0);

            // Indexes
            $table->index('franchise_id');
            $table->index('business_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
