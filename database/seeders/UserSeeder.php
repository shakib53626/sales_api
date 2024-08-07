<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name'         => 'Super Admin',
            'phone_number' => '01784801663',
            'email'        => 'superadmin@gmail.com',
            'status'       => 'active',
            "password"     => Hash::make(123456789)
        ]);

        $user->roles()->attach(1);
    }
}
