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
        Schema::table('services', function (Blueprint $table) {
            // Supprimer l'ancienne colonne status
            $table->dropColumn('status');
        });

        Schema::table('services', function (Blueprint $table) {
            // Recréer la colonne avec les nouveaux statuts
            $table->enum('status', ['en_attente', 'published', 'archived', 'rejected'])
                ->default('en_attente')
                ->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
