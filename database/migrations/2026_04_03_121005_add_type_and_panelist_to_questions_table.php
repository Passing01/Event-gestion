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
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', ['question', 'contribution'])->default('question')->after('content');
            $table->foreignId('panelist_id')->nullable()->constrained('panelists')->nullOnDelete()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('panelist_id');
            $table->dropColumn('type');
        });
    }
};
