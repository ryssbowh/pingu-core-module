<?php

namespace Pingu\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Pingu\Forms\Fields\Number;
use Pingu\Forms\Fields\Text;
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
                'Section' => 'Core',
                'type' => Text::class,
                'validation' => 'required'
            ],
            'session.lifetime' => [
                'Title' => 'Session Lifetime',
                'Section' => 'Core',
                'type' => Number::class,
                'validation' => 'required|integer'
            ]
        ]);

        Permission::findOrCreate(['name' => 'access admin area', 'section' => 'Core']);
        Permission::findOrCreate(['name' => 'view site in maintenance mode', 'section' => 'Core']);
    }
}
