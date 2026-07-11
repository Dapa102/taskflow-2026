<?php

use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tasks') || !Schema::hasColumn('tasks', 'status')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('draft','assigned_pm','assigned_member','todo','on_progress','in_progress','pending_pm','pending_admin','pending_arbitration','revision','review','done','cancelled') NOT NULL DEFAULT 'todo'");
        }

        DB::table('tasks')->whereIn('status', ['draft', 'assigned_pm', 'assigned_member', 'todo'])->update(['status' => TaskStatus::TODO]);
        DB::table('tasks')->whereIn('status', ['on_progress', 'revision'])->update(['status' => TaskStatus::IN_PROGRESS]);
        DB::table('tasks')->whereIn('status', ['pending_pm', 'pending_admin', 'pending_arbitration'])->update(['status' => TaskStatus::REVIEW]);
        DB::table('tasks')->where('status', 'cancelled')->update(['status' => TaskStatus::CANCELLED]);
        DB::table('tasks')->where('status', 'done')->update(['status' => TaskStatus::DONE]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo','in_progress','review','done','cancelled') NOT NULL DEFAULT 'todo'");
        }
    }

    public function down(): void
    {
        // MVP status strings are compatible with the base schema string column.
    }
};
