<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'npk' => 'P1111',
                'name' => 'Amelia',
                'dept' => '4141',
                'sect' => 'Non BaaN',
                'golongan' => 1,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P1122',
                'name' => 'Ghina',
                'dept' => '4141',
                'sect' => 'Kadept',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P1133',
                'name' => 'Zakiya',
                'dept' => '4141',
                'sect' => 'Kadiv',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P1144',
                'name' => 'Fikri',
                'dept' => '4141',
                'sect' => 'DIC',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P2211',
                'name' => 'Farhan',
                'dept' => '6121',
                'sect' => 'PIC',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P2222',
                'name' => 'Charnawan',
                'dept' => '6121',
                'sect' => 'Kadept',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P3311',
                'name' => 'Nina',
                'dept' => '1111',
                'sect' => 'Non BaaN',
                'golongan' => 1,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P3322',
                'name' => 'Syahnaz',
                'dept' => '1111',
                'sect' => 'Kadept',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P3333',
                'name' => 'Rangga',
                'dept' => '1111',
                'sect' => 'Kadiv',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
            [
                'npk' => 'P3344',
                'name' => 'Syamsul',
                'dept' => '1111',
                'sect' => 'DIC',
                'golongan' => 2,
                'acting' => 1,
                'password' => 'admin',
            ],
        ];
        

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
