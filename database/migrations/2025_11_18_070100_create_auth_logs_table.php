<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('auth_logs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('user_id', 36);
            $table->char('franchise_id', 36);
            $table->char('business_unit_id', 36);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('event_type', 50);
            $table->timestamp('event_time')->useCurrent();
            $table->string('apitoken', 255)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'franchise_id', 'business_unit_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('auth_logs');
    }
};