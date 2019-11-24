<?php 

namespace Pingu\Core\Config;

use Pingu\Core\Settings\SettingsRepository;
use Pingu\Field\BaseFields\Integer;
use Pingu\Field\BaseFields\Model;
use Pingu\Field\BaseFields\Text;
use Pingu\User\Entities\Role;
use Pingu\User\Entities\User;

class CoreSettings extends SettingsRepository
{
    protected $casts = [
        'session.lifetime' => 'Session Lifetime',
        'user.guestRole' => 'model:'.Role::class
    ];
    protected $accessPermission = 'view general settings';
    protected $editPermission = 'view general settings';
    protected $titles = [
        'app.name' => 'Site name',
        'session.lifetime' => 'Session Lifetime',
        'core.maintenance.message' => 'Maintenance mode message',
        'user.guestRole' => 'Guest role'
    ];
    protected $keys = ['app.name', 'session.lifetime', 'core.maintenance.message', 'user.guestRole'];
    protected $validations = [
        'app_name' => 'required|string',
        'core_maintenance_message' => 'required|string',
        'session_lifetime' => 'required|integer|min:0',
        'user.guestRole' => 'required|integer'
    ];
    protected $units = [
        'session.lifetime' => 'Minutes'
    ];
    protected $helpers = [
        'session.lifetime' => 'Controls how long before users have to login again',
        'user.guestRole' => 'Do not change this unless you know what you\'re doing'
    ];

    public function section(): string
    {
        return 'General';
    }

    public function name(): string
    {
        return 'general';
    }

    protected function fields(): array
    {
        return [
            new Text(
                'app.name',
                [
                    'required' => true,
                    'label' => $this->getFieldLabel('app.name'),
                ]
            ),
            new Integer(
                'session.lifetime',
                [
                    'label' => $this->getFieldLabel('session.lifetime'),
                    'helper' => $this->helper('session.lifetime'),
                    'required' => true, 
                    'min' => 0
                ]
            ),
            new Text(
                'core.maintenance.message',
                [
                    'required' => true, 
                    'label' => $this->getFieldLabel('core.maintenance.message'),
                ]
            ),
            new Model(
                'user.guestRole',
                [
                    'helper' => $this->helper('user.guestRole'),
                    'label' => $this->getFieldLabel('user.guestRole'),
                    'model' => Role::class,
                    'textField' => 'name',
                    'required' => true
                ]
            )
        ];
    }
}