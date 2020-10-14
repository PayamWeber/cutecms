<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'nick_name' => $faker->firstNameMale,
        'email' => $faker->unique()->safeEmail,
        'password' => Hash::make('123456789'), // secret
        'remember_token' => str_random(10),
        'role_id' => optional(\App\Models\Role::all())->random(1)->id,
        'capabilities' => [],
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
