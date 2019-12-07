<?php

namespace Pingu\Core\Settings;

use Illuminate\Foundation\Application;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Forms\SettingsForm;
use Pingu\Forms\Support\FormElement;
use Pingu\Menu\Entities\MenuItem;
use Pingu\Permissions\Entities\Permission;

abstract class SettingsRepository
{
    /**
     * Config keys defined in this repository
     *
     * @var array
     */
    protected $keys = [];
    /**
     * Validation rules
     *
     * @var array
     */
    protected $validations = [];
    /**
     * Units for keys
     *
     * @var array
     */
    protected $units = [];
    /**
     * Helpers for keys
     *
     * @var array
     */
    protected $helpers = [];
    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];
    /**
     * Titles for keys
     *
     * @var array
     */
    protected $titles = [];
    /**
     * List of encrypted keys
     *
     * @var array
     */
    protected $encrypteds = [];
    /**
     * Castings for keys
     *
     * @var array
     */
    protected $casts = [];
    /**
     * Edit permission
     *
     * @var string
     */
    protected $editPermission = '';
    /**
     * Access permission
     *
     * @var string
     */
    protected $accessPermission = '';
    /**
     * List of fields
     *
     * @var array
     */
    protected $fields;

    public function __construct()
    {
        if ($validations = $this->validations()) {
            $this->validations = $validations;
        }
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
     * Fields for this repository
     * 
     * @return array
     */
    protected abstract function fields(): array;

    protected function validations(): array
    {
        return [];
    }

    /**
     * Permission to view this repository settings
     * 
     * @return array
     */
    public function accessPermission(): string
    {
        return $this->accessPermission;
    }

    /**
     * Permission to edit this repository settings
     * 
     * @return array
     */
    public function editPermission(): string
    {
        return $this->editPermission;
    }

    /**
     * Permission setter
     * 
     * @param string $permission
     *
     * @return SettingsRepository
     */
    public function setAccessPermission(string $permission)
    {
        $this->accessPermission = $permission;
        return $this;
    }

    /**
     * Permission setter
     * 
     * @param string $permission
     *
     * @return SettingsRepository
     */
    public function setEditPermission(string $permission)
    {
        $this->editPermission = $permission;
        return $this;
    }

    /**
     * Fields getter
     * 
     * @return array
     */
    public function getFields()
    {
        return $this->resolveFields();
    }

    /**
     * Add a field to this repository
     * 
     * @param BaseField $field
     *
     * @return SettingsRepository
     */
    public function addField(BaseField $field)
    {
        $this->resolveFields();
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Edit form for this repository keys
     * 
     * @param array $action
     * 
     * @return SettingsForm
     */
    public function editForm(array $action): SettingsForm
    {
        return new SettingsForm($action, $this);
    }

    /**
     * Creates this repository keys in database.
     * Will create the permissions if $perm is true.
     * Will create a menu item if $item is true
     * Typically to be used in a seeder
     *
     * @param bool $perm
     * @param bool $item
     */
    public function create($perm = true, $item = true)
    {
        foreach ($this->keys() as $key) {
            \Settings::create($key, $this->name(), $this->encrypted($key));
        }

        if ($perm) {
            $permission = Permission::findOrCreate(['name' => $this->accessPermission(), 'section' => 'Settings']);
            Permission::findOrCreate(['name' => $this->editPermission(), 'section' => 'Settings']);
        }

        if ($item) {
            MenuItem::create(
                [
                'name' => $this->section(),
                'active' => 1,
                'url' => '/'.adminPrefix().'/settings/'.$this->name(),
                'deletable' => 0,
                'permission_id' => $permission ? $permission->id : null
                ], 'admin-menu', 'admin-menu.settings'
            );
        }
    }

    /**
     * Friendly value for a key
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function friendlyValue(string $name)
    {
        $value = config($name);
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        } elseif ($value instanceof BaseModel) {
            return $value->getDescription();
        }
        return $value;
    }

    /**
     * Get all keys
     * 
     * @return array
     */
    public function keys()
    {
        return $this->keys;
    }

    /**
     * Add a cast to this repository
     * 
     * @param string $name
     */
    public function addCast(string $name, string $cast)
    {
        $this->casts[$name] = $cast;
        return $this;
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
        return in_array($name, $this->encrypteds);
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

    public function cast($name, $value)
    {
        if (!isset($this->casts[$name])) {
            return $value;
        }
        return $this->performCast($this->casts[$name], $value);
    }

    /**
     * Returns the fields for that repository
     * 
     * @return array
     */
    protected function resolveFields()
    {
        if (is_null($this->fields)) {
            $this->fields = $this->fields();
        }
        return $this->fields;
    }

    /**
     * Cast a value to a native PHP type.
     *
     * @param string $key
     * @param mixed  $value
     * 
     * @return mixed
     */
    protected function performCast($cast, $value)
    {
        $elems = explode(':', $cast);
        $cast = $elems[0];
        $args = $elems[1] ?? null;
        switch ($cast) {
        case 'int':
        case 'integer':
            return (int) $value;
        case 'real':
        case 'float':
        case 'double':
            return (float)$value;
        case 'decimal':
            return $this->asDecimal($value, $args);
        case 'bool':
        case 'boolean':
            return (bool) $value;
        case 'model':
            return $this->asModel($value, $args);
        case 'array':
        case 'json':
            return json_decode($value, true);
        case 'date':
            return $this->asDate($value);
        case 'datetime':
            return $this->asDateTime($value);
        case 'timestamp':
            return $this->asTimestamp($value);
        default:
            return $value;
        }
    }

    /**
     * Cast a value into a BaseModel
     * 
     * @param int    $value
     * @param string $model
     * 
     * @return BaseModel
     */
    protected function asModel($value, $model)
    {
        return $model::find($value);
    }

    /**
     * Return a decimal as string.
     *
     * @param float $value
     * @param int   $decimals
     * 
     * @return string
     */
    protected function asDecimal($value, $decimals)
    {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * Return a timestamp as unix timestamp.
     *
     * @param mixed $value
     * 
     * @return int
     */
    protected function asTimestamp($value)
    {
        return $this->asDateTime($value)->getTimestamp();
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param mixed $value
     * 
     * @return \Illuminate\Support\Carbon
     */
    protected function asDateTime($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->startOfDay();
    }

    /**
     * Return a timestamp as DateTime object with time set to 00:00:00.
     *
     * @param mixed $value
     * 
     * @return \Illuminate\Support\Carbon
     */
    protected function asDate($value)
    {
        return $this->asDateTime($value)->startOfDay();
    }
}