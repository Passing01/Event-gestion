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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable()->after('description');
            }
        });

        Schema::table('panelists', function (Blueprint $table) {
            if (!Schema::hasColumn('panelists', 'presentation_duration')) {
                $table->integer('presentation_duration')->default(0)->after('user_id');
            }
            if (!Schema::hasColumn('panelists', 'presentation_started_at')) {
                $table->dateTime('presentation_started_at')->nullable()->after('presentation_duration');
            }
            if (!Schema::hasColumn('panelists', 'is_document_shared')) {
                $table->boolean('is_document_shared')->default(false)->after('presentation_path');
            }
            if (!Schema::hasColumn('panelists', 'is_projecting')) {
                $table->boolean('is_projecting')->default(false)->after('is_document_shared');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });

        Schema::table('panelists', function (Blueprint $table) {
            $table->dropColumn(['presentation_duration', 'presentation_started_at', 'is_document_shared', 'is_projecting']);
        });
    }
};
