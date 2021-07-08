<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role' => 'company',
            'full_name' => Str::random(10),
            'email' => Str::random(10). '@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'profile_image'=>'admin.png',
            // 'phone' => $this->$faker->phoneNumber,
            // 'birth_year' => '1991',
            'remember_token' => Str::random(10),
            'company_activity' => Str::random(10),
            'company_description' => Str::random(100),
            'employees_number' => 10,
            'pib' => 101,
            'pdv' => 123,
            // 'package_id' => 1,
            // 'country_id' => 1,
            // 'city_id' => 1,
            'address' => Str::random(10).' street',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
