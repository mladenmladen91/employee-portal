<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Gender;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        // Gender::create([
        //     'name' => 'muški'
        //  ]);

        // Gender::create([
        //     'name' => 'ženski'
        //  ]);

        // User::create([
        //     'full_name' => 'admin',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('admin12345678'),
        //     'role' => 'admin'
        // ]);

        // User::create([
        //     'full_name' => 'company',
        //     'email' => 'company@example.com',
        //     'password' => bcrypt('company12345678'),
        //     'role' => 'company'
        // ]);

        // User::create([
        //     'full_name' => 'employee',
        //     'email' => 'employee@example.com',
        //     'password' => bcrypt('employee12345678'),
        //     'role' => 'employee'
        // ]);

    }
}
