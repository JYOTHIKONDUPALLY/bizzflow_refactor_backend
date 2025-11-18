<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change tokenable_id from unsignedBigInteger to CHAR(36) to support UUIDs
        DB::statement("ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` CHAR(36) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to unsigned big integer
        DB::statement("ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` BIGINT UNSIGNED NOT NULL");
    }
};
