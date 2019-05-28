<?php

namespace Pingu\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Pingu\Forms\Fields\Number;
use Pingu\Forms\Fields\Text;
use Pingu\Menu\Entities\Menu;
use Pingu\Menu\Entities\MenuItem;
use Pingu\Permissions\Entities\Permission;
use Settings;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::registerMany([
            'app.name' => [
                'Title' => 'Site name',
                'Section' => 'core',
                'type' => Text::class,
                'validation' => 'required'
            ],
            'session.lifetime' => [
                'Title' => 'Session Lifetime',
                'Section' => 'core',
                'type' => Number::class,
                'validation' => 'required|integer'
            ],
            'core.maintenance.message' => [
                'Title' => 'Maintenance mode message',
                'Section' => 'core',
                'type' => Text::class,
                'validation' => 'required'
            ]
        ]);

        $perm1 = Permission::findOrCreate(['name' => 'access admin area', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'view debug bar', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'edit core settings', 'section' => 'Core']);
        $perm2 = Permission::findOrCreate(['name' => 'view core settings', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'view site in maintenance mode', 'section' => 'Core']);

        $main = Menu::where(['machineName' => 'main-menu'])->first();

        if(!$main){
            $main = Menu::create([
                'machineName' => 'main-menu',
                'name' => 'Main Menu'
            ]);

            MenuItem::create([
                'name' => 'Admin',
                'weight' => 1,
                'active' => 1,
                'url' => settings('adminPrefix'),
                'permission_id' => $perm1->id
            ], $main);
        }

        $admin = Menu::where(['machineName' => 'admin-menu'])->first();

        if(!$admin){
            $admin = Menu::create([
                'machineName' => 'admin-menu',
                'name' => 'Amin Menu'
            ]);
            $settings = MenuItem::create([
                'name' => 'Settings',
                'weight' => 1,
                'active' => 1,
                'permission_id' => null
            ], $admin);
            MenuItem::create([
                'name' => 'Core',
                'weight' => 1,
                'active' => 1,
                'url' => 'settings.admin.core',
                'permission_id' => $perm2->id
            ], $admin, $settings);
        }
    }
}