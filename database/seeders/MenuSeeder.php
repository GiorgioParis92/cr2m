<?php

// database/seeders/MenuSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        Menu::create([
            'title' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'dashboard_icon', // Update with actual icon if needed
            'is_active' => true,
            'parent_id' => null,
        ]);

        Menu::create([
            'title' => 'Tables',
            'url' => 'tables',
            'icon' => 'tables_icon', // Update with actual icon if needed
            'is_active' => false,
            'parent_id' => null,
        ]);

        // Add more menu items as needed
    }
}

