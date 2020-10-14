<?php

use App\Models\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'nick_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make( '123456789' ), // secret
            'remember_token' => str_random( 10 ),
            'role_id' => optional( Role::findByName( 'administrator' ) )->id,
            'capabilities' => '[]',
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);
        User::factory()->setRole( 'blog' )->count(9)->create();
    }
}
