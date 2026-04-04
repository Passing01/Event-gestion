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
        Schema::table('event_participants', function (Blueprint $table) {
            // Add a simple index on event_id because MySQL needs it for the FK
            $table->index('event_id');
            // Drop old unique index
            $table->dropUnique(['event_id', 'pseudo']);
            // Add date column for the join day
            $table->date('joined_date')->nullable()->after('pseudo');
            // Add new unique index including the day
            $table->unique(['event_id', 'pseudo', 'joined_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_participants', function (Blueprint $table) {
             $table->dropUnique(['event_id', 'pseudo', 'joined_date']);
             $table->dropColumn('joined_date');
             $table->unique(['event_id', 'pseudo']);
        });
    }
};
