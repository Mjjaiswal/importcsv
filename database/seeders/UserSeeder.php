<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
        User::create([
            'name' => 'Exporter User',
            'email' => 'exporter@example.com',
            'password' => Hash::make('Password@123'),
            'role' => 'exporter',
        ]);

        User::create([
            'name' => 'Importer User',
            'email' => 'importer@example.com',
            'password' => Hash::make('Password@123'),
            'role' => 'importer',
        ]);
    }
}
