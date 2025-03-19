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
        // Étape 1: Ajouter la colonne si elle n'existe pas
        if (!Schema::hasColumn('offres', 'recruter_id')) {
            Schema::table('offres', function (Blueprint $table) {
                $table->unsignedBigInteger('recruter_id')->nullable();
            });
        }

        // Étape 2: Ajouter la contrainte de clé étrangère
        Schema::table('offres', function (Blueprint $table) {
            try {
                $table->foreign('recruter_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            } catch (\Exception $e) {
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['recruter_id']);

            // Supprimer la colonne
            $table->dropColumn('recruter_id');
        });
    }
};
