<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'cinemasaguilasuas@admin.com'], 
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin'), 
                'role' => 'admin', 
            ]
        );
    }
}
