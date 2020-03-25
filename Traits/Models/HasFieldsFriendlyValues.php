<?php

namespace Pingu\Core\Traits\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Pingu\User\Entities\User;

trait HasFieldsFriendlyValues
{
    /**
     * Friendly value for an attribute
     * 
     * @param string $key
     * 
     * @return mixed
     */
    public function getFriendlyValue($key)
    {
        $value = $this->getAttributeFromArray($key);
        
        if (\in_array($key, $this->getDates())) {
            if (! \is_null($value)) {
                $value = $this->asDateTime($value);
            }
        }

        if ($this->hasFriendlyMutator($key)) {
            return $this->mutateFriendlyAttribute($key, $value);
        }

        return \data_get($this, $key);
    }

    /**
     * Detect whether key has friendly mutator.
     *
     * @param string  $key
     *
     * @return bool
     */
    public function hasFriendlyMutator(string $key): bool
    {
        return \method_exists($this, 'friendly'.Str::studly($key).'Attribute');
    }

    /**
     * Mutate friendly field attribute.
     *
     * @param string  $key
     * @param mixed  $value
     *
     * @return mixed
     */
    private function mutateFriendlyAttribute(string $key, $value)
    {
        return $this->{'friendly'.Str::studly($key).'Attribute'}($value);
    }
}