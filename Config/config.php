<?php

return [
    'name' => 'Core',
    'homepage' => '/home',
    'adminTheme' => 'Admin',
    'frontTheme' => 'Default',
    'jsGridDefaults' => [
    	"autoload" => true,
    	"width" => "100%",
    	"height" => "auto",
    	"heading" => true,
    	"filtering" => true,
    	"inserting" => false,
    	"editing" => true,
    	"selecting" => true,
    	"sorting" => true,
    	"paging" => true,
    	"pageLoading" => true,
    	"noDataContent" => "Not found",
    	"deleteConfirm" => "Are you sure?",
    	"confirmDeleting" => true,
    	"pagerContainer" => null,
    	"pageIndex" => 1,
    	"pageSize" => 20,
    	"pageButtonCount" => 15,
    	"pagerFormat" => "Pages: {first} {prev} {pages} {next} {last}    {pageIndex} of {pageCount}",
    	"pagePrevText" => "Prev",
    	"pageNextText" => "Next",
    	"pageFirstText" => "First",
    	"pageLastText" => "Last",
    	"pageNavigatorNextText" => "...",
    	"pageNavigatorPrevText" => "...",
    	"invalidMessage" => "Invalid data entered!",
    	"loadIndication" => true,
    	"loadIndicationDelay" => 500,
    	"loadMessage" => "Please, wait...",
    	"loadShading" => true,
    	"updateOnResize" => true
    ],
    'themes' => [
        'themes_path' => 'themes',
        'views_path' => 'views',
        'asset_path' => 'assets',
        'images_path' => 'images',

        'modules_namespaced_views' => 'modules',

        /*
        |--------------------------------------------------------------------------
        | Set behavior if an asset is not found in a Theme hierarchy.
        | Available options: THROW_EXCEPTION | LOG_ERROR | IGNORE
        |--------------------------------------------------------------------------
        */

        'asset_not_found' => 'LOG_ERROR',

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

        /*
        |--------------------------------------------------------------------------
        | Define available themes. Format:
        |
        |   'theme-name' => [
        |       'extends'       => 'theme-to-extend',  // optional
        |       'views-path'    => 'path-to-views',    // defaults to: resources/views/theme-name
        |       'asset-path'    => 'path-to-assets',   // defaults to: public/theme-name
        |
        |       // You can add your own custom keys
        |       // Use Theme::getSetting('key') & Theme::setSetting('key', 'value') to access them
        |       'key'           => 'value',
        |   ],
        |
        |--------------------------------------------------------------------------
        */

        'themes' => [

                    // Add your themes here. These settings will override theme.json settings defined for each theme

            /*
            |---------------------------[ Example Structure ]--------------------------
            |
            |   // Full theme Syntax:
            |
            |   'example1' => [
            |       'extends'       => null,    // doesn't extend any theme
            |       'views-path'    => example, // = resources/views/example_theme
            |       'asset-path'    => example, // = public/example_theme
            |   ],
            |
            |   // Use all Defaults:
            |
            |   'example2', // Assets =\public\example2, Views =\resources\views\example2
            |               // Note that if you use all default values, you can omit declaration completely.
            |               // i.e. defaults will be used when you call Theme::set('undefined-theme')
            |
            |
            |   // This theme shares the views with example2 but defines its own assets in \public\example3
            |
            |   'example3' => [
            |       'views-path'    => 'example',
            |   ],
            |
            |   // This theme extends example1 and may override SOME views\assets in its own paths
            |
            |   'example4' => [
            |       'extends'   => 'example1',
            |   ],
            |
            |--------------------------------------------------------------------------
            */
        ]
    ]
];
