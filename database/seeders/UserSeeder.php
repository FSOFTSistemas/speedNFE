<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        'name' => 'admin master',
        'email' => 'master@admin.com',
        'password' => Hash::make('admin1234'), // Sempre criptografe a senha!
        'cargo' => 'master',
        'empresa_id' => 1
    ]);
}

}
