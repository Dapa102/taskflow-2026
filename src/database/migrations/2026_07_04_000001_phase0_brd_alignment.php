<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // Step 0.1 — tasks: tambah field BRD (skip kalo udah ada)
        // ============================================================
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'recommended_pm_id')) {
                $table->foreignId('recommended_pm_id')->nullable()->constrained('users')->after('created_by');
            }
            if (!Schema::hasColumn('tasks', 'assigned_pm_id')) {
                $table->foreignId('assigned_pm_id')->nullable()->constrained('users')->after('assigned_to');
            }
            if (!Schema::hasColumn('tasks', 'assigned_member_id')) {
                $table->foreignId('assigned_member_id')->nullable()->constrained('users')->after('assigned_pm_id');
            }
            if (!Schema::hasColumn('tasks', 'revision_counter')) {
                $table->unsignedTinyInteger('revision_counter')->default(0)->after('reviewed_by');
            }
            if (!Schema::hasColumn('tasks', 'max_revision_limit')) {
                $table->unsignedTinyInteger('max_revision_limit')->default(3)->after('revision_counter');
            }
            if (!Schema::hasColumn('tasks', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('deadline');
            }
            if (!Schema::hasColumn('tasks', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('tasks', 'cancellation_note')) {
                $table->text('cancellation_note')->nullable()->after('escalated_at');
            }
            if (!Schema::hasColumn('tasks', 'arbitration_decision')) {
                $table->enum('arbitration_decision', ['approved', 'return_to_revision'])->nullable()->after('cancellation_note');
            }
        });

        // Step 0.2 — Migrasi status: dari legacy 6 status ke 9 status BRD
        // Cek apakah status sudah berisi 'draft' atau belum
        $statusCol = DB::select("SHOW COLUMNS FROM tasks WHERE Field = 'status'")[0]->Type ?? '';
        if (strpos($statusCol, 'draft') === false) {
            DB::statement("UPDATE tasks SET status = 'draft' WHERE status = 'todo'");
            DB::statement("UPDATE tasks SET status = 'assigned_member' WHERE status = 'on_progress'");
            DB::statement("
                ALTER TABLE tasks
                MODIFY COLUMN status ENUM(
                    'draft','assigned_pm','assigned_member','pending_pm',
                    'revision','pending_arbitration','pending_admin','done','cancelled'
                ) NOT NULL DEFAULT 'draft'
            ");
        }

        // ============================================================
        // Step 0.3 — task_status_histories table
        // ============================================================
        if (!Schema::hasTable('task_status_histories')) {
            Schema::create('task_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained()->onDelete('cascade');
                $table->string('from_status', 30);
                $table->string('to_status', 30);
                $table->foreignId('changed_by')->constrained('users');
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['task_id', 'created_at']);
            });
        }

        // ============================================================
        // Step 0.4 — inbox_notifications table
        // ============================================================
        if (!Schema::hasTable('inbox_notifications')) {
            Schema::create('inbox_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade');
                $table->enum('channel', ['whatsapp', 'email', 'inbox']);
                $table->string('subject', 200);
                $table->text('message')->nullable();
                $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status', 'created_at']);
            });
        }

        // ============================================================
        // Step 0.5 — users: validasi field
        // ============================================================
        if (!Schema::hasColumn('users', 'nomor_whatsapp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nomor_whatsapp', 20)->nullable()->after('phone');
            });
        }

        // role: mapping ke BRD + update ENUM
        DB::statement("UPDATE users SET role = 'super_admin' WHERE role = 'admin'");
        DB::statement("UPDATE users SET role = 'member' WHERE role NOT IN ('super_admin', 'pm', 'member')");
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('super_admin', 'pm', 'member') DEFAULT 'member'
        ");

        // ============================================================
        // Step 0.6 — workspaces: deputy_pm_id
        // ============================================================
        if (!Schema::hasColumn('workspaces', 'deputy_pm_id')) {
            Schema::table('workspaces', function (Blueprint $table) {
                $table->foreignId('deputy_pm_id')->nullable()->constrained('users')->after('pm_id');
            });
        }
    }

    public function down(): void
    {
        // Reverse Step 0.6
        if (Schema::hasColumn('workspaces', 'deputy_pm_id')) {
            Schema::table('workspaces', function (Blueprint $table) {
                $table->dropForeign(['deputy_pm_id']);
                $table->dropColumn('deputy_pm_id');
            });
        }

        // Reverse Step 0.5
        if (Schema::hasColumn('users', 'nomor_whatsapp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nomor_whatsapp');
            });
        }
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) DEFAULT 'member'");

        // Reverse Step 0.4
        Schema::dropIfExists('inbox_notifications');

        // Reverse Step 0.3
        Schema::dropIfExists('task_status_histories');

        // Reverse Step 0.2
        DB::statement("UPDATE tasks SET status = 'todo' WHERE status = 'draft'");
        DB::statement("UPDATE tasks SET status = 'on_progress' WHERE status = 'assigned_member'");
        $statusCol = DB::select("SHOW COLUMNS FROM tasks WHERE Field = 'status'")[0]->Type ?? '';
        if (strpos($statusCol, 'todo') === false) {
            DB::statement("
                ALTER TABLE tasks
                MODIFY COLUMN status ENUM('todo','on_progress','pending_pm','pending_admin','revision','done')
                NOT NULL DEFAULT 'todo'
            ");
        }

        // Reverse Step 0.1
        $columnsToDrop = [
            'recommended_pm_id', 'assigned_pm_id', 'assigned_member_id',
            'revision_counter', 'max_revision_limit', 'submitted_at',
            'escalated_at', 'cancellation_note', 'arbitration_decision',
        ];
        $existing = array_filter($columnsToDrop, fn($c) => Schema::hasColumn('tasks', $c));
        if (!empty($existing)) {
            Schema::table('tasks', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
