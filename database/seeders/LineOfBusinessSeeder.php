<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LineOfBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('line_of_businesses')->insert([
            // Kategori Umum
            ['lob_id' => '1', 'line_business' => '2W', 'status' => 1],
            ['lob_id' => '2', 'line_business' => '4W', 'status' => 1],
            ['lob_id' => '3', 'line_business' => 'FF', 'status' => 1],
            ['lob_id' => '4', 'line_business' => 'OCU', 'status' => 1],
            ['lob_id' => '5', 'line_business' => 'SA', 'status' => 1],
            ['lob_id' => '6', 'line_business' => 'COMM', 'status' => 1],
        ]);
    }
}
