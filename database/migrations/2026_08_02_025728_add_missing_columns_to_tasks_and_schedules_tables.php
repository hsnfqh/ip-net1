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
        Schema::table('tasks', function (Blueprint $table) {
            // Hanya tambahkan kolom jika belum ada
            if (!Schema::hasColumn('tasks', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('tasks', 'engineer_id')) {
                $table->unsignedBigInteger('engineer_id')->nullable()->after('project_id');
            }
            if (!Schema::hasColumn('tasks', 'title')) {
                $table->string('title')->nullable()->after('engineer_id');
            }
            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->string('priority')->nullable()->after('title');
            }
            if (!Schema::hasColumn('tasks', 'status')) {
                $table->string('status')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('tasks', 'deadline')) {
                $table->date('deadline')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tasks', 'progress')) {
                $table->integer('progress')->default(0)->after('deadline');
            }
            if (!Schema::hasColumn('tasks', 'attachments')) {
                $table->integer('attachments')->default(0)->after('progress');
            }
            if (!Schema::hasColumn('tasks', 'description')) {
                $table->text('description')->nullable()->after('attachments');
            }
            if (!Schema::hasColumn('tasks', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('description');
            }
            if (!Schema::hasColumn('tasks', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('schedules', 'engineer_id')) {
                $table->unsignedBigInteger('engineer_id')->nullable()->after('project_id');
            }
            if (!Schema::hasColumn('schedules', 'title')) {
                $table->string('title')->nullable()->after('engineer_id');
            }
            if (!Schema::hasColumn('schedules', 'date')) {
                $table->date('date')->nullable()->after('title');
            }
            if (!Schema::hasColumn('schedules', 'start_time')) {
                $table->time('start_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('schedules', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('schedules', 'location')) {
                $table->string('location')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('schedules', 'description')) {
                $table->text('description')->nullable()->after('location');
            }
            if (!Schema::hasColumn('schedules', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('description');
            }
            if (!Schema::hasColumn('schedules', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kosong karena semua kolom untuk tasks dan schedules sudah ditambahkan
        // di migration create table masing-masing
    }
};
