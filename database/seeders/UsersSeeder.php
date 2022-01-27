<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        DB::table('users')->updateOrInsert([
            'phone' => '254700016349'
        ], [
            'email' => 'admin@diskus-analytics.com',
            'name' => 'Diskus Admin',
            'role_id' => Role::firstOrCreate(['name' => Role::SUPER_ROLE])->id,
            'password' => Hash::make('turtledove')
        ]);
    }
}
