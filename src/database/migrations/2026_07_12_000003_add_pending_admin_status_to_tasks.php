<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'status')) {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo','in_progress','review','pending_admin','done','cancelled') NOT NULL DEFAULT 'todo'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'status')) {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo','in_progress','review','done','cancelled') NOT NULL DEFAULT 'todo'");
        }
    }
};
