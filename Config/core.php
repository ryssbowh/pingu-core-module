<?php

return [
    'name' => 'Core',
    'homepage' => '/',
    /**
     * Prefix for ajax routes
     */
    'ajaxPrefix' => 'ajax',
    /**
     * Config to be loaded in javascript
     */
    'ajaxConfig' => [],
    /**
     * Prefix for admin routes
     */
    'adminPrefix' => 'admin',
    /**
     * Prefix for api routes
     */
    'apiPrefix' => 'api',
    'views' => [
        'suggestionsCacheKey' => 'core.views.suggestions'
    ],
    'settings' => [
        'useCache' => true
    ]
];
