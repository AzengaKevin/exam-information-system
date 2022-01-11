<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'phone' => '254700016349',
            'role_id' => Role::firstOrCreate(['name' => 'Administrator'])->id
        ]);

        // Create Super Admin
        User::factory()->create([
            'name' => 'Diskus Administrator',
            'email' => 'azenga.kevin7@gmail.com',
            'phone' => '254114023230',
            'role_id' => Role::firstOrCreate(['name' => 'Administrator'])->id,
            'password' => Hash::make('elephant69')
        ]);
    }
}
