<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = ['Toshkent', 'Andijon', 'Buxoro', 'Farg\'ona', 'Jizzax', 'Namangan', 'Navoiy', 'Qashqadaryo', 'Samarqand', 'Sirdaryo', 'Surxondaryo', 'Xorazm'];
        foreach ($regions as $region) {
            \App\Models\Region::create(['name' => $region]);
        }
    }
}
