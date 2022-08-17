<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        \App\Models\User::create([
            'firstname' => 'Brian',
            'lastname' => 'Rabin',
            'email' => 'brianrabin@elink.com.ph',
            'email_verified_at' => now(),
            'password' => Hash::make('1234'),
        ]);
    }
}
