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
        Schema::table('replies', function (Blueprint $table) {
            if (!Schema::hasColumn('replies', 'audio_path')) {
                $table->string('audio_path')->nullable()->after('content');
            }
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replies', function (Blueprint $table) {
            if (Schema::hasColumn('replies', 'audio_path')) {
                $table->dropColumn('audio_path');
            }
            $table->text('content')->nullable(false)->change();
        });
    }
};
