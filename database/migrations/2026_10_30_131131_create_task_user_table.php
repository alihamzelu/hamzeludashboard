<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
        });

        if (Schema::hasColumn('tasks', 'assigned_to')) {
            DB::table('tasks')
                ->select('id', 'assigned_to')
                ->whereNotNull('assigned_to')
                ->orderBy('id')
                ->chunkById(500, function ($tasks) {
                    $now = now();

                    $rows = $tasks->map(fn ($task) => [
                        'task_id' => $task->id,
                        'user_id' => $task->assigned_to,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    DB::table('task_user')->insertOrIgnore($rows);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('task_user');
    }
};