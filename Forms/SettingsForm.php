<?php

namespace Pingu\Core\Forms;

use Pingu\Core\Settings\SettingsRepository;
use Pingu\Forms\Fields\Base\Email;
use Pingu\Forms\Fields\Base\Text;
use Pingu\Forms\Support\Fields\Hidden;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class SettingsForm extends Form
{
    /**
     * Bring variables in your form through the constructor :
     */
    public function __construct(array $action, SettingsRepository $repository)
    {
        $this->action = $action;
        $this->repository = $repository;
        parent::__construct();
    }

    /**
     * Fields definitions for this form, classes used here
     * must extend Pingu\Forms\Support\Field
     * 
     * @return array
     */
    public function elements(): array
    {
        $fields = $this->repository->getFields();
        $elems = [];
        foreach ($fields as $field) {
            $field->option('default', config($field->machineName()));
            $elems[] = $field->toFormElement();
        }
        $elems[] = new Submit();
        return $elems;
    }

    /**
     * Method for this form, POST GET DELETE PATCH and PUT are valid
     * 
     * @return string
     */
    public function method(): string
    {
        return 'PUT';
    }

    /**
     * Url for this form, valid values are
     * ['url' => '/foo.bar']
     * ['route' => 'login']
     * ['action' => 'MyController@action']
     * 
     * @return array
     * 
     * @see https://github.com/LaravelCollective/docs/blob/5.6/html.md
     */
    public function action(): array
    {
        return $this->action;
    }

    /**
     * Name for this form, ideally it would be application unique, 
     * best to prefix it with the name of the module it's for.
     * 
     * @return string
     */
    public function name(): string
    {
        return 'edit-settings';
    }
}