<?php 

namespace Pingu\Core\Config;

use Pingu\Core\Settings\SettingsRepository;
use Pingu\Forms\Support\Fields\NumberInput;
use Pingu\Forms\Support\Fields\TextInput;

class CoreSettings extends SettingsRepository
{
    public function section(): string
    {
        return 'General';
    }

    public function name(): string
    {
        return 'general';
    }

    public function accessPermissions(): array 
    {
        return ['view general settings'];
    }

    public function editPermissions(): array 
    {
        return ['edit general settings'];
    }

    protected function titles(): array
    {
        return [
            'app.name' => 'Site name',
            'session.lifetime' => 'Session Lifetime',
            'core.maintenance.message' => 'Maintenance mode message',
        ];
    }

    protected function keys(): array
    {
        return ['app.name', 'session.lifetime', 'core.maintenance.message'];
    }

    protected function validations(): array
    {
        return [
            'app_name' => 'required|string',
            'core_maintenance_message' => 'required|string',
            'session_lifetime' => 'required|integer|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
        ];
    }

    protected function units(): array
    {
        return [
            'session.lifetime' => 'Minutes'
        ];
    }

    protected function helpers(): array
    {
        return [
            'session.lifetime' => 'Controls how long before users have to login again'
        ];
    }

    protected function fields(): array
    {
        return [
            new TextInput(
                'app.name',
                ['required' => true]
            ),
            new NumberInput(
                'session.lifetime',
                [
                    'label' => $this->getFieldLabel('session.lifetime'),
                    'helper' => $this->helper('session.lifetime'),
                    'required' => true, 
                ],
                [
                    'min' => 0
                ]
            ),
            new TextInput(
                'core.maintenance.message',
                [
                    'required' => true
                ]
            )
        ];
    }
}