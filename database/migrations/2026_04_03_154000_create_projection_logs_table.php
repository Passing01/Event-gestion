<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projection_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('event_id')->constrained()->cascadeOnDelete();
            $blueprint->foreignId('panelist_id')->constrained()->cascadeOnDelete();
            $blueprint->integer('slide_number');
            $blueprint->timestamps();
        });
        
        // On va aussi ajouter une colonne à la table events pour savoir si il est sur le marketplace
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_on_marketplace')->default(false);
            $table->decimal('marketplace_price', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projection_logs');
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_on_marketplace', 'marketplace_price']);
        });
    }
};
