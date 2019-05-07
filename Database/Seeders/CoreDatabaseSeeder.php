<?php

namespace Pingu\Core\Database\Seeders;

use Pingu\Forms\Fields\Number;
use Pingu\Forms\Fields\Text;
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
    }
}
