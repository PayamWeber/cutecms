<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\Role;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    public $role = 'administrator';

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'nick_name' => $this->faker->firstNameMale,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make( '123456789' ), // secret
            'remember_token' => str_random( 10 ),
            'role_id' => optional( Role::findByName( $this->role ) )->id,
            'capabilities' => [],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setRole( $name )
    {
        $this->role_id = $name;
        return $this;
    }
}
