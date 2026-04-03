<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable();
            $table->text('ai_summary')->nullable();
            $table->json('ai_keywords')->nullable();
            $table->string('ai_sentiment')->nullable();
            $table->longText('ai_report')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['closed_at', 'ai_summary', 'ai_keywords', 'ai_sentiment', 'ai_report']);
        });
    }
};
