<?php

use Pingu\Core\Config\CoreSettings;
use Pingu\Core\Seeding\DisableForeignKeysTrait;
use Pingu\Core\Seeding\MigratableSeeder;
use Pingu\Forms\Support\Fields\Email;
use Pingu\Forms\Support\Fields\NumberInput;
use Pingu\Forms\Support\Fields\Password;
use Pingu\Forms\Support\Fields\TextInput;
use Pingu\Menu\Entities\Menu;
use Pingu\Menu\Entities\MenuItem;
use Pingu\Permissions\Entities\Permission;
use Pingu\Settings\Forms\Types\Integer;
use Pingu\Settings\Forms\Types\Text;

class S2019_08_06_171621248759_InstallCore extends MigratableSeeder
{
    use DisableForeignKeysTrait;

    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        Permission::findOrCreate(['name' => 'browse site', 'section' => 'Core']);
        $perm1 = Permission::findOrCreate(['name' => 'access admin area', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'view debug bar', 'section' => 'Core', 'helper' => 'This should only be for developers']);
        Permission::findOrCreate(['name' => 'view site in maintenance mode', 'section' => 'Core', 'helper' => 'Login will always be available in maintenance mode']);
        Permission::findOrCreate(['name' => 'put site in maintenance mode', 'section' => 'Core']);

        $perm4 = Permission::findOrCreate(['name' => 'view modules', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'activate modules', 'section' => 'Core']);

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
                'deletable' => false,
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
            $structure = MenuItem::create([
                'name' => 'Structure',
                'weight' => 2,
                'active' => 1,
                'deletable' => 0,
                'permission_id' => null
            ], $admin);
            MenuItem::create([
                'name' => 'Modules',
                'weight' => 3,
                'active' => 1,
                'deletable' => 0,
                'url' => 'core.admin.modules',
                'permission_id' => $perm4->id
            ], $admin);
        }

        \Settings::repository('general')->create();
        \Settings::repository('mailing')->create();
    }

    /**
     * Reverts the database seeder.
     */
    public function down(): void
    {
        // Remove your data
    }
}
