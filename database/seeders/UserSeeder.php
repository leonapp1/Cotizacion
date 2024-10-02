<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si el usuario ya existe para evitar duplicados
        $user = User::where('email', 'bellcor@bellcor.com')->first();

        if (!$user) {
            // Crear el usuario con la contraseña especificada
            User::create([
                'name' => 'Bellcor User',
                'email' => 'bellcor@bellcor.com',
                'password' => Hash::make('b3llc0r'),
            ]);

            $this->command->info('Usuario creado con email bellcor@bellcor.com y contraseña b3llc0r');
        } else {
            $this->command->info('El usuario con email bellcor@bellcor.com ya existe.');
        }
    }
}
