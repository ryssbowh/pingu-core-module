<?php 

namespace Pingu\Core\Validation;

class CoreValidationRules
{
    /**
     * Validates an url
     * 
     * @param string $attribute
     * @param mixed  $value
     * @param array  $parameters
     * @param Validator $validator
     * 
     * @return bool
     */
    public function validUrl($attribute, $value, $parameters, $validator) 
    {
        if (!isset($parameters[0])) {
            $parameters[0] = 'both';
        }
        if ($parameters[0] == 'internal') {
            $res = $this->validateInternalUrl($value);
        } elseif ($parameters[0] == 'external') {
            $res = $this->isExternalUrl($value);
        }
        $res = ($this->isInternalUrl($value) or $this->isExternalUrl($value));
        if (!$res) {
            $validator->setCustomMessages(['valid_url' => ':attribute is not a valid url']);
        }
        return $res;
    }

    /**
     * Is an url internal (starts with / or is a route name)
     * 
     * @param string  $url
     * 
     * @return boolean
     */
    protected function isInternalUrl(string $url)
    {
        return (substr($url, 0, 1) == '/' or route_exists($url));
    }

    /**
     * Is an url external
     * 
     * @param string  $url
     * 
     * @return boolean
     */
    protected function isExternalUrl(string $url)
    {
        return (substr($url, 0, 7) == 'http://' or substr($url, 0, 7) == 'https://');
    }
}