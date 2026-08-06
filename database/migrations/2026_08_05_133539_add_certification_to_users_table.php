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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'certification')) {
                $table->string('certification')->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'certification_file')) {
                $table->string('certification_file')->nullable()->after('certification');
            }
            if (!Schema::hasColumn('users', 'certification_status')) {
                $table->string('certification_status')->nullable()->default('pending')->after('certification_file');
            }
            if (!Schema::hasColumn('users', 'certification_uploaded_at')) {
                $table->timestamp('certification_uploaded_at')->nullable()->after('certification_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['certification', 'certification_file', 'certification_status', 'certification_uploaded_at']);
        });
    }
};
