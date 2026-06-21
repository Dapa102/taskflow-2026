<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('review_note')->nullable()->after('description');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->after('assigned_to');
        });

        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo','on_progress','pending_pm','pending_admin','revision','done') NOT NULL DEFAULT 'todo'");
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['review_note', 'reviewed_by']);
        });

        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo','on_progress','done') NOT NULL DEFAULT 'todo'");
    }
};
