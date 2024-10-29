<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some top-level menus
        Menu::factory()->count(5)->create();

        // Create some submenus for each top-level menu
        Menu::all()->each(function ($menu) {
            Menu::factory()->count(3)->create();
        });
    }
}
