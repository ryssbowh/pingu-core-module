<?php

namespace Pingu\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Pingu\Forms\Fields\Number;
use Pingu\Forms\Support\Fields\NumberInput;
use Pingu\Forms\Support\Fields\TextInput;
use Pingu\Forms\Support\Types\Integer;
use Pingu\Forms\Support\Types\Text;
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
                'field' => TextInput::class,
                'type' => Text::class,
                'validation' => 'required'
            ],
            'session.lifetime' => [
                'Title' => 'Session Lifetime',
                'Section' => 'core',
                'field' => NumberInput::class,
                'type' => Integer::class,
                'validation' => 'required|integer'
            ],
            'core.maintenance.message' => [
                'Title' => 'Maintenance mode message',
                'Section' => 'core',
                'field' => TextInput::class,
                'type' => Text::class,
                'validation' => 'required'
            ]
        ]);

        Permission::findOrCreate(['name' => 'browse site', 'section' => 'Core']);
        $perm1 = Permission::findOrCreate(['name' => 'access admin area', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'view debug bar', 'section' => 'Core', 'helper' => 'This should only be for developers']);
        Permission::findOrCreate(['name' => 'edit core settings', 'section' => 'Core']);
        $perm2 = Permission::findOrCreate(['name' => 'view core settings', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'view site in maintenance mode', 'section' => 'Core', 'helper' => 'Login will always be available in maintenance mode']);
        Permission::findOrCreate(['name' => 'put site in maintenance mode', 'section' => 'Core']);

        $main = Menu::where(['machineName' => 'main-menu'])->first();

        if(!$main){
            $main = Menu::create([
                'machineName' => 'main-menu',
                'name' => 'Main Menu',
                'deletable' => 0
            ]);

            MenuItem::create([
                'name' => 'Admin',
                'weight' => 1,
                'active' => 1,
                'url' => 'admin',
                'permission_id' => $perm1->id
            ], $main);
        }

        $admin = Menu::where(['machineName' => 'admin-menu'])->first();

        if(!$admin){
            $admin = Menu::create([
                'machineName' => 'admin-menu',
                'name' => 'Amin Menu',
                'deletable' => 0
            ]);
            $settings = MenuItem::create([
                'name' => 'Settings',
                'weight' => 1,
                'active' => 1,
                'deletable' => 0,
                'permission_id' => null
            ], $admin);
            MenuItem::create([
                'name' => 'Core',
                'weight' => 1,
                'active' => 1,
                'url' => 'settings.admin.core',
                'deletable' => 0,
                'permission_id' => $perm2->id
            ], $admin, $settings);
            MenuItem::create([
                'name' => 'Structure',
                'weight' => 2,
                'active' => 1,
                'deletable' => 0,
                'permission_id' => null
            ], $admin);
        }
    }
}
