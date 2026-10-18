<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        DB::table('conversations')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($conversations) {
                foreach ($conversations as $conversation) {
                    DB::table('conversations')
                        ->where('id', $conversation->id)
                        ->update([
                            'uuid' => (string) Str::uuid(),
                        ]);
                }
            });

        Schema::table('conversations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};