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
     * Maintenance mode config
     */
    'maintenance' => [
        'view' => 'core::maintenance-mode',
        'retryAfter' => '1800',
        'message' => 'This site is in maintenance, please try again later'
    ],
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
    'themes' => [
        'views_path' => 'views',
        'asset_path' => 'assets',
        /**
         * Public path for themes assets linkage
         */
        'public_path' => 'themes',

        /**
         * Folder for module views within themes
         */
        'modules_namespaced_views' => 'modules',

        /*
        |--------------------------------------------------------------------------
        | Set behavior if an asset is not found in a Theme hierarchy.
        | Available options: THROW_EXCEPTION | LOG_ERROR | IGNORE
        |--------------------------------------------------------------------------
        */

        'asset_not_found' => (env('APP_ENV') == 'local') ? 'THROW_EXCEPTION' : 'LOG_ERROR',

        /*
        |--------------------------------------------------------------------------
        | Do we want a theme activated by default? Can be set at runtime with:
        | Theme::set('theme-name');
        |--------------------------------------------------------------------------
        */

        'default' => null,

        /*
        |--------------------------------------------------------------------------
        | Cache theme.json configuration files that are located in each theme's folder
        | in order to avoid searching theme settings in the filesystem for each request
        |--------------------------------------------------------------------------
        */

        'cache' => false,
    ],
    'seeders' => [
        'table' => 'seedings',
        'dir' => 'seeds',
        'namespace' => 'App\database\seeds'
    ]
];
