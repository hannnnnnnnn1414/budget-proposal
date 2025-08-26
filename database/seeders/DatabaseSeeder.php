<?php

namespace Database\Seeders;

use App\Models\BudgetCode;
use App\Models\Departments;
use App\Models\LineOfBusiness;
use App\Models\User;
use App\Models\Workcenter;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            LineOfBusinessSeeder::class,
            AccountSeeder::class,
            ItemSeeder::class,
            WorkcenterSeeder::class,
            UserSeeder::class,
            DepartmentSeeder::class,
            BudgetCodeSeeder::class
        ]);
    }
}
