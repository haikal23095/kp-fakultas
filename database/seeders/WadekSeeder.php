<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WadekSeeder extends Seeder
{
    public function run()
    {
        
        DB::table('Users')->insert([
            [
                'Id_User'   => 310, 
                'Username'  => 'wadek2',
                'password'  => Hash::make('password_wadek2'),
                'Name_User' => 'Dr. M. Sultan Syahputra, S.T., M.T.',
                'Id_Role'   => 9,
                'email'     => 'sultan.syahputra@fakultas.ac.id',
                'No_WA'     => '081255667788',
            ],
            [
                'Id_User'   => 311, 
                'Username'  => 'wadek3',
                'password'  => Hash::make('password_wadek3'),
                'Name_User' => 'Dr. Wiwik Amalia, S.Kom., M.Kom.',
                'Id_Role'   => 10,
                'email'     => 'wiwik.amalia@fakultas.ac.id',
                'No_WA'     => '081399881122',
            ]
        ]);
    }
}