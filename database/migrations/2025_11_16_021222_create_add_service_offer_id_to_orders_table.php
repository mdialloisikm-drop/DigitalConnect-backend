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
        Schema::table('orders', function (Blueprint $table) {
            // Supprimer l'ancienne colonne service_id si elle existe
            if (Schema::hasColumn('orders', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }

            // Ajouter service_offer_id comme seule référence
            $table->foreignId('service_offer_id')
                ->after('id')
                ->constrained('service_offers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_service_offer_id_to_orders');
    }
};
