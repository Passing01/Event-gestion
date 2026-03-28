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
            $table->string('brand_color')->nullable();
            $table->string('projection_layout')->nullable();
            $table->boolean('default_moderation')->default(true);
            $table->string('plan')->default('free');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['brand_color', 'projection_layout', 'default_moderation', 'plan']);
        });
    }
};
