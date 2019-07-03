<?php

namespace Pingu\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Pingu\Forms\Fields\Number;
use Pingu\Forms\Support\Fields\Email;
use Pingu\Forms\Support\Fields\NumberInput;
use Pingu\Forms\Support\Fields\Password;
use Pingu\Forms\Support\Fields\TextInput;
use Pingu\Menu\Entities\Menu;
use Pingu\Menu\Entities\MenuItem;
use Pingu\Permissions\Entities\Permission;
use Pingu\Settings\Forms\Types\Integer;
use Pingu\Settings\Forms\Types\Text;
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
                'Section' => 'general',
                'field' => TextInput::class, 
                'type' => Text::class,
                'validation' => 'required|string',
                'attributes' => ['required' => true],
                'weight' => 0
            ],
            'session.lifetime' => [
                'Title' => 'Session Lifetime',
                'helper' => 'Controls how long before users have to login again',
                'Section' => 'general',
                'unit' => 'seconds',
                'field' => NumberInput::class,
                'type' => Integer::class,
                'validation' => 'required|integer|min:36000',
                'attributes' => ['required' => true, 'min' => 3600],
                'weight' => 1
            ],
            'core.maintenance.message' => [
                'Title' => 'Maintenance mode message',
                'Section' => 'general',
                'field' => TextInput::class,
                'type' => Text::class,
                'validation' => 'required|string',
                'attributes' => ['required' => true],
                'weight' => 2
            ],
            'mail.host' => [
                'Title' => 'Mail host',
                'Section' => 'mailing',
                'field' => TextInput::class,
                'type' => Text::class,
                'validation' => 'required|string',
                'attributes' => ['required' => true],
                'weight' => 0
            ],
            'mail.port' => [
                'Title' => 'Mail port',
                'Section' => 'mailing',
                'field' => NumberInput::class,
                'type' => Integer::class,
                'validation' => 'required|integer',
                'attributes' => ['required' => true],
                'weight' => 1
            ],
            'mail.username' => [
                'Title' => 'Mail username',
                'Section' => 'mailing',
                'field' => TextInput::class,
                'type' => Text::class,
                'validation' => 'required|string',
                'attributes' => ['required' => true],
                'weight' => 2
            ],
            'mail.password' => [
                'Title' => 'Mail password',
                'Section' => 'mailing',
                'field' => Password::class,
                'type' => Text::class,
                'validation' => 'required|string',
                'encrypted' => true,
                'attributes' => ['required' => true],
                'weight' => 3
            ],
            'mail.from.address' => [
                'Title' => 'Email address from',
                'Section' => 'mailing',
                'field' => Email::class,
                'type' => Text::class,
                'validation' => 'required|email',
                'attributes' => ['required' => true],
                'weight' => 4
            ],
            'mail.from.name' => [
                'Title' => 'Email name from',
                'Section' => 'mailing',
                'field' => TextInput::class,
                'type' => Text::class,
                'validation' => 'required|string',
                'attributes' => ['required' => true],
                'weight' => 5
            ]
        ]);

        Permission::findOrCreate(['name' => 'browse site', 'section' => 'Core']);
        $perm1 = Permission::findOrCreate(['name' => 'access admin area', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'view debug bar', 'section' => 'Core', 'helper' => 'This should only be for developers']);
        $perm2 = Permission::findOrCreate(['name' => 'view general settings', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'edit general settings', 'section' => 'Core']);
        $perm3 = Permission::findOrCreate(['name' => 'view mailing settings', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'edit mailing settings', 'section' => 'Core']);
        
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
            MenuItem::create([
                'name' => 'General',
                'weight' => 1,
                'active' => 1,
                'url' => 'settings.admin.general',
                'deletable' => 0,
                'permission_id' => $perm2->id
            ], $admin, $settings);
            MenuItem::create([
                'name' => 'Mailing',
                'weight' => 1,
                'active' => 1,
                'url' => 'settings.admin.mailing',
                'deletable' => 0,
                'permission_id' => $perm3->id
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
