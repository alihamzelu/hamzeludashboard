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
            $table->boolean('notify_email')->default(true)->after('email');
            $table->boolean('notify_task_assigned')->default(true);
            $table->boolean('notify_due_reminders')->default(true);
            $table->boolean('notify_weekly_digest')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_email',
                'notify_task_assigned',
                'notify_due_reminders',
                'notify_weekly_digest',
            ]);
        });
    }
};
