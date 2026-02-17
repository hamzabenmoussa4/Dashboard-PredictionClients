<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // On vérifie si l'admin existe déjà (évite les doublons)
        $admin = User::where('email', 'admin@admin.com')->first();

        if ($admin) {
            // Si l'admin existe déjà, on s'assure qu'il est bien admin
            $admin->is_admin = true;
            $admin->save();
            return;
        }

        // Création de l'utilisateur admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin12345'),
            'is_admin' => true,
        ]);
    }
}
