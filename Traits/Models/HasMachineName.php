<?php

namespace Pingu\Core\Traits\Models;

trait HasMachineName
{
    /**
     * Get all machine names
     * 
     * @return array
     */
    public static function allMachineNames()
    {
        return static::all()->pluck('machineName');
    }

    /**
     * Find a model by machine name
     *
     * @param  string $name
     * @return ImageStyle|null
     */
    public static function findByMachineName(string $name)
    {
        return static::where(['machineName' => $name])->first();
    }

    /**
     * Suffixes $name with a number to make it unique
     * 
     * @param  string $name
     * @return string
     */
    public function getUniqueMachineName(string $name)
    {
        $item = $this::findByMachineName($name);
        if(!$item) {
            return $name;
        }

        if(substr($name, -2, 1) == '_') {
            $number = (int)substr($name, -1);
            $name = trim($name, $number).($number + 1);
        }
        else{
            $name .= "_1";
        }

        return $this->getUniqueMachineName($name);
    }
}