<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin420',
            'email' => 'Admin420@BCV.com',
            'password' => hash('md5', "AdminKomweb476"),
            'status' => 'admin'
        ]);
    }
}
