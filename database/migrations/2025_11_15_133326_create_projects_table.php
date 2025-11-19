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
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('progress')->default(0)->after('status');
        });
        Schema::table('projects', function (Blueprint $table) {
            // Supprimer l'ancienne colonne status
            $table->dropColumn('status');
        });

        Schema::table('projects', function (Blueprint $table) {
            // Recréer la colonne avec les nouveaux statuts
            $table->enum('status', ['en_attente', 'open', 'in_progress', 'completed', 'cancelled', 'archived'])
                ->default('en_attente')
                ->after('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
