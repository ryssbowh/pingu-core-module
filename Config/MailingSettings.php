<?php 

namespace Pingu\Core\Config;

use Pingu\Core\Settings\SettingsRepository;
use Pingu\Forms\Support\Fields\Email;
use Pingu\Forms\Support\Fields\NumberInput;
use Pingu\Forms\Support\Fields\Password;
use Pingu\Forms\Support\Fields\TextInput;

class MailingSettings extends SettingsRepository
{
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

    /**
     * @inheritDoc
     */
    public function accessPermissions(): array 
    {
        return ['view mailing settings'];
    }

    /**
     * @inheritDoc
     */
    public function editPermissions(): array 
    {
        return ['edit mailing settings'];
    }

    /**
     * @inheritDoc
     */
    protected function titles(): array
    {
        return [
            'mail.host' => 'Mail host',
            'mail.port' => 'Mail port',
            'mail.username' => 'Mail username',
            'mail.password' => 'Mail password',
            'mail.from.address' => 'Email address from',
            'mail.from.name' => 'Email name from'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function keys(): array
    {
        return ['mail.host', 'mail.port', 'mail.username', 'mail.password', 'mail.from.address', 'mail.from.name'];
    }

    /**
     * @inheritDoc
     */
    protected function validations(): array
    {
        return [
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer|min:0',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_from.address' => 'required|string|email',
            'mail_from.name' => 'required|string'
        ];
    }

    protected function encrypteds(): array
    {
        return ['mail.password'];
    }

    public function fields(): array
    {
        return [
            new TextInput(
                'mail.host',
                [
                    'label' => $this->getFieldLabel('mail.host'),
                    'required' => true
                ]
            ),
            new NumberInput(
                'mail.port',
                [
                    'label' => $this->getFieldLabel('mail.port'),
                    'required' => true
                ]
            ),
            new TextInput(
                'mail.username',
                [
                    'label' => $this->getFieldLabel('mail.username'),
                    'required' => true
                ]
            ),
            new Password(
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
            new TextInput(
                'mail.from.name',
                [
                    'label' => $this->getFieldLabel('mail.from.name'),
                    'required' => true
                ]
            )
        ];
    }
}