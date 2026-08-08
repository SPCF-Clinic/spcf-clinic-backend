<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bed;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beds = 20; // Number of beds to seed

        for ($i = 1; $i <= $beds; $i++) {
            Bed::create([
                'bed_number' => 'Bed ' . $i,
                'status' => 'Empty',
            ]);
        }
    }
}
