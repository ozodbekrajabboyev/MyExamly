<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            "Qoraqalpogʻiston Respublikasi",
            "Andijon viloyati",
            "Buxoro viloyati",
            "Fargʻona viloyati",
            "Jizzax viloyati",
            "Xorazm viloyati",
            "Namangan viloyati",
            "Navoiy viloyati",
            "Qashqadaryo viloyati",
            "Samarqand viloyati",
            "Sirdaryo viloyati",
            "Surxondaryo viloyati",
            "Toshkent viloyati",
            "Toshkent shahri"
        ];

        foreach ($regions as $region) {
            Region::create(['name' => $region]);
        }
    }
}
