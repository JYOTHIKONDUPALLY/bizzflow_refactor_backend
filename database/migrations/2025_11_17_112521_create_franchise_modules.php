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
        Schema::create('franchise_modules', function (Blueprint $table) {
      // Primary key UUID
            $table->char('id', 36)->primary();

            // Foreign key UUIDs
            $table->char('franchise_id', 36);
            $table->char('business_unit_id', 36)->nullable();

            // Fields
            $table->string('module_name', 100);
            $table->boolean('is_enabled')->default(true);
            $table->json('config')->nullable();

            // Audit fields - UUIDs
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();

            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // Indexes if needed
            $table->index('franchise_id');
            $table->index('business_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_modules');
    }
};
