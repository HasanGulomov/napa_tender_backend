<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = ['UZEX', 'Tender Week', 'IT Market', 'XT-Xarid'];
        foreach ($sources as $source) {
            \App\Models\Source::create(['name' => $source]);
        }
    }
}
