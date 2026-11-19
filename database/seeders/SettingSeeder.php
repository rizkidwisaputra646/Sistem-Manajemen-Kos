<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ['key' => 'hero_title', 'value' => 'Cari Kontrakan Tanpa Ribet.'],
            ['key' => 'hero_subtitle', 'value' => 'Platform reservasi hunian online terbaik...'],
            ['key' => 'address', 'value' => 'Jl. Ryacudu, Bandar Lampung'],
            ['key' => 'about_text', 'value' => 'KosKu adalah solusi hunian modern...'],
        ];

        foreach ($data as $item) {
            \App\Models\Setting::create($item);
        }
    }
}
