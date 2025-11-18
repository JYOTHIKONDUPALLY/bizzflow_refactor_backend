<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('personal_access_tokens');
    }
    public function down(): void {
        // No-op: do not restore table
    }
};