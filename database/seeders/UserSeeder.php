<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Matheus Martinez',
            'email' => 'matheusmartinez.1@hotmail.com.br',
            'password' => bcrypt('1234'),
        ]);
    }
}
