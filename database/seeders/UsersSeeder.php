<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin
        User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'admin@gmail.com',
            'role_id' => Role::firstOrCreate(['name' => 'Administrator'])->id
        ]);
    }
}
