<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('assigned_pm_id', 'idx_tasks_assigned_pm_id');
            $table->index('assigned_member_id', 'idx_tasks_assigned_member_id');
            $table->index(['deadline', 'status'], 'idx_tasks_deadline_status');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_assigned_pm_id');
            $table->dropIndex('idx_tasks_assigned_member_id');
            $table->dropIndex('idx_tasks_deadline_status');
        });
    }
};
