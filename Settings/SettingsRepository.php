<?php

namespace Pingu\Core\Settings;

use Illuminate\Foundation\Application;
use Pingu\Core\Forms\SettingsForm;
use Pingu\Forms\Support\FormElement;

abstract class SettingsRepository
{
    protected $validations;
    protected $units;
    protected $fields;
    protected $helpers;
    protected $keys;
    protected $messages;
    protected $titles;
    protected $encrypteds;

    /**
     * Boots this repository
     */
    public function boot()
    {
        $this->validations = $this->validations();
        $this->units = $this->units();
        $this->helpers = $this->helpers();
        $this->messages = $this->messages();
        $this->keys = $this->keys();
        $this->titles = $this->titles();
        $this->encrypteds = $this->encrypteds();
        $this->fields = $this->fields();
    }

    /**
     * Unique name for this repository
     *
     * @return string
     */
    public abstract function name(): string;

    /**
     * Section (title) for this repository
     * 
     * @return string
     */
    public abstract function section(): string;

    /**
     * Permission(s) to view this repository settings
     * 
     * @return array
     */
    public abstract function accessPermissions(): array;

    /**
     * Permission(s) to edit this repository settings
     * 
     * @return array
     */
    public abstract function editPermissions(): array;

    /**
     * Config keys handled by this repository
     * 
     * @return array
     */
    protected abstract function keys(): array;

    /**
     * Fields for this repository
     * 
     * @return array
     */
    protected abstract function fields(): array;

    /**
     * Titles for this repository keys
     * 
     * @return array
     */
    protected function titles(): array
    {
        return [];
    }

    /**
     * Validation rules for this repository keys.
     * Dots in keys must be replaced with underscores here
     * 
     * @return array
     */
    protected function validations(): array
    {
        return [];
    }

    /**
     * Units for this repository keys
     * 
     * @return array
     */
    protected function units(): array
    {
        return [];
    }

    /**
     * Helpers for this repository keys
     * 
     * @return array
     */
    protected function helpers(): array
    {
        return [];
    }

    /**
     * Validation messages for this repository keys.
     * Dots in keys must be replaced with underscores here
     * 
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Keys that are encrypted
     * 
     * @return array
     */
    protected function encrypteds(): array
    {
        return [];
    }

    /**
     * Edit form for this repository keys
     * 
     * @param  array  $action
     * 
     * @return SettingsForm
     */
    public function editForm(array $action): SettingsForm
    {
        return new SettingsForm($action, $this);
    }

    /**
     * Creates this repository keys in database.
     * Typically to be used in a seeder
     */
    public function create()
    {
        foreach ($this->getKeys() as $key) {
            \Settings::create($key, $this->name(), $this->encrypted($key));
        }
    }

    /**
     * Value for a key
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function value(string $name)
    {
        return config($name);
    }

    /**
     * Get all keys
     * 
     * @return array
     */
    public function getKeys()
    {
        return $this->keys();
    }

    /**
     * Add a key to this repository
     * 
     * @param string $name
     */
    public function addKey(string $name)
    {
        $this->keys[] = $name;
        return $this;
    }

    /**
     * Unit getter
     * 
     * @param  string|null $name
     * @return string|array
     */
    public function unit(?string $name = null)
    {
        if (is_null($name)) {
            return $this->units;
        }
        return $this->units[$name] ?? '';
    }

    /**
     * Add a unit to this repository
     * 
     * @param string $name
     * @param string $unit
     *
     * @return SettingsRepository
     */
    public function addUnit(string $name, string $unit)
    {
        $this->units[$name] = $unit;
        return $this;
    }

    /**
     * Field(s) getter
     * 
     * @param  string|null $name
     * @return array|FormElement|null
     */
    public function getFields(?string $name = null)
    {
        if (is_null($name)) {
            return $this->fields;
        }
        return $this->fields[$name] ?? null;
    }

    /**
     * Add a field to this repository
     * 
     * @param string $name
     * @param FormElement $field
     *
     * @return SettingsRepository
     */
    public function addField(string $name, FormElement $field)
    {
        $this->fields[$name] = $field;
        return $this;
    }

    /**
     * Helper getter
     * 
     * @param  string|null $name
     * @return string|array
     */
    public function helper(?string $name = null)
    {
        if (is_null($name)) {
            return $this->helpers;
        }
        return $this->helpers[$name] ?? '';
    }

    /**
     * Add a helper to this repository
     * 
     * @param string $name
     * @param string $helper
     *
     * @return SettingsRepository
     */
    public function addHelper(string $name, string $helper)
    {
        $this->helpers[$name] = $helper;
        return $this;
    }

    /**
     * Encrypted getter
     * 
     * @param string|null $name
     * 
     * @return bool|array
     */
    public function encrypted(?string $name = null)
    {
        if (is_null($name)) {
            return $this->encrypteds;
        }
        return isset($this->encrypteds[$name]);
    }

    /**
     * Add an key to the encrypted array
     * 
     * @param string $name
     *
     * @return SettingsRepository
     */
    public function addEncrypted(string $name)
    {
        $this->encrypteds[] = $name;
        return $this;
    }

    public function getMessages(?string $name = null)
    {
        return $this->messages;
    }

    /**
     * Add a validation message to this repository
     * 
     * @param string $name
     * @param string $message
     *
     * @return SettingsRepository
     */
    public function addMessage(string $name, string $message)
    {
        $this->messages[$name] = $message;
        return $this;
    }

    /**
     * Title getter
     * 
     * @param  string|null $name
     * @return string|array
     */
    public function title(?string $name = null)
    {
        if (is_null($name)) {
            return $this->titles;
        }
        return $this->titles[$name] ?? $name;
    }

    /**
     * Add a title to this repository
     * 
     * @param string $name
     * @param string $unit
     *
     * @return SettingsRepository
     */
    public function addTitle(string $name, string $title)
    {
        $this->titles[$name] = $title;
        return $this;
    }

    /**
     * Get validations messages
     * 
     * @return array
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * Add validation message to this repository
     * 
     * @param string $name
     * @param string $validation
     *
     * @return SettingsRepository
     */
    public function addValidation(string $name, string $validation)
    {
        $this->validations[$name] = $title;
        return $this;
    }

    /**
     * Builds a field label with this repository titles and units
     * 
     * @param string $name
     * 
     * @return string
     */
    protected function getFieldLabel(string $name)
    {
        $title = $this->title($name);
        if ($unit = $this->unit($name)) {
            $title .= ' ('.$unit.')';
        }
        return $title;
    }
}