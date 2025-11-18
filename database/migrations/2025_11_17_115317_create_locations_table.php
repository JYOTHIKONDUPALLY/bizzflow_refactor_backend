<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->char('id', 36)->primary();

            $table->char('franchise_id', 36);
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();

            $table->char('country_id', 36);
            $table->char('currency_id', 36);
            $table->string('timezone', 100);

            $table->char('address_id', 36)->nullable();

            $table->boolean('is_active')->default(1);

            $table->timestamps();

            // Indexes
            $table->index('franchise_id');
            $table->index('country_id');
            $table->index('currency_id');
            $table->index('address_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
