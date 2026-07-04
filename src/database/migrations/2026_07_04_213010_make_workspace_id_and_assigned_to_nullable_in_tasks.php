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
            $table->dropForeign(['workspace_id']);
            $table->dropForeign(['assigned_to']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->change();
            $table->foreignId('assigned_to')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('workspace_id')->references('id')->on('workspaces')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
            $table->dropForeign(['assigned_to']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable(false)->change();
            $table->foreignId('assigned_to')->nullable(false)->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
