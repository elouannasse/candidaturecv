<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer d'abord les rôles
        DB::table('roles')->insert([
            ['name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Recruteur', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Candidat', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Ensuite créer l'utilisateur avec un rôle spécifique
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => 1, // Admin
        ]);
    }
}