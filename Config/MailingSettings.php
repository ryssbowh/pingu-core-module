<?php 

namespace Pingu\Core\Config;

use Pingu\Core\Settings\SettingsRepository;
use Pingu\Field\BaseFields\Email;
use Pingu\Field\BaseFields\Integer;
use Pingu\Field\BaseFields\Password;
use Pingu\Field\BaseFields\Text;

class MailingSettings extends SettingsRepository
{
    protected $accessPermission = 'view mailing settings';
    protected $editPermission = 'view mailing settings';
    protected $titles = [
        'mail.host' => 'Mail host',
        'mail.port' => 'Mail port',
        'mail.username' => 'Mail username',
        'mail.password' => 'Mail password',
        'mail.from.address' => 'Email address from',
        'mail.from.name' => 'Email name from'
    ];
    protected $keys = ['mail.host', 'mail.port', 'mail.username', 'mail.password', 'mail.from.address', 'mail.from.name'];
    protected $validations = [
        'mail_host' => 'required|string',
        'mail_port' => 'required|integer|min:0',
        'mail_username' => 'required|string',
        'mail_password' => 'required|string',
        'mail_from_address' => 'required|string|email',
        'mail_from_name' => 'required|string'
    ];
    protected $encrypteds = ['mail.password'];

    /**
     * @inheritDoc
     */
    public function section(): string
    {
        return 'Mailing';
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'mailing';
    }

    public function fields(): array
    {
        return [
            new Text(
                'mail.host',
                [
                    'label' => $this->getFieldLabel('mail.host'),
                    'required' => true
                ]
            ),
            new Integer(
                'mail.port',
                [
                    'label' => $this->getFieldLabel('mail.port'),
                    'required' => true,
                    'min' => 0
                ]
            ),
            new Text(
                'mail.username',
                [
                    'label' => $this->getFieldLabel('mail.username'),
                    'required' => true
                ]
            ),
            new Text(
                'mail.password',
                [
                    'label' => $this->getFieldLabel('mail.password'),
                    'required' => true
                ]
            ),
            new Email(
                'mail.from.address',
                [
                    'label' => $this->getFieldLabel('mail.from.address'),
                    'required' => true
                ]
            ),
            new Text(
                'mail.from.name',
                [
                    'label' => $this->getFieldLabel('mail.from.name'),
                    'required' => true
                ]
            )
        ];
    }
}