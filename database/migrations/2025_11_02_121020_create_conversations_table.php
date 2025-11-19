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
        Schema::table('conversations', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère sur project_id
            $table->dropForeign(['project_id']);

            // Supprimer la colonne project_id
            $table->dropColumn('project_id');


            // Ajouter un index unique pour éviter les conversations dupliquées
            $table->unique(['client_id', 'freelance_id'], 'unique_conversation_participants');

            // Ajouter un index pour les recherches par statut
            $table->index('status');

            // Ajouter un index pour les recherches par date
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
