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
        Schema::table('panelists', function (Blueprint $table) {
            $table->unsignedInteger('current_page')->default(1)->after('is_projecting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panelists', function (Blueprint $table) {
            $table->dropColumn('current_page');
        });
    }
};
