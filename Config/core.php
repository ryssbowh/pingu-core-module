<?php

return [
    'name' => 'Core',
    'homepage' => '/',
    /**
     * Back end theme
     */
    'adminTheme' => 'Admin',
    /**
     * Front end theme
     */
    'frontTheme' => 'Front',
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

    'seeders' => [
        'table' => 'seedings',
        'dir' => 'seeds',
        'namespace' => 'App\database\seeds'
    ],
    'views' => [
        'suggestionsCacheKey' => 'core.views.suggestions'
    ],
    'settings' => [
        'useCache' => !env('APP_DEBUG')
    ]
];
