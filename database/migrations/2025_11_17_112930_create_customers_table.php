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
        Schema::create('customers', function (Blueprint $table) {

            // Primary key UUID
            $table->char('id', 36)->primary();

            // Foreign keys (UUID)
            $table->char('franchise_id', 36);
            $table->char('business_unit_id', 36)->nullable();

            // Customer data
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();

            // Soft delete style flag
            $table->boolean('is_deleted')->default(0);

            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate();

            // Indexes
            $table->index('franchise_id');
            $table->index('business_unit_id');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
