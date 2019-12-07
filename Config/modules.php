<?php

return [
    'stubs' => [
        'enabled' => true,
        'path' => base_path('Modules/Core/Console/stubs/modules'),
        'files' => [
            'routes/web' => 'Routes/web.php',
            'routes/api' => 'Routes/api.php',
            'routes/admin' => 'Routes/admin.php',
            'routes/ajax' => 'Routes/ajax.php',
            'scaffold/config' => 'Config/config.php',
            'composer' => 'composer.json',
            'providers/event' => 'Providers/EventServiceProvider.php',
            'providers/auth' => 'Providers/AuthServiceProvider.php',
            'assets/js/app' => 'Resources/assets/js/app.js',
            'assets/sass/app' => 'Resources/assets/sass/app.scss',
            'webpack' => 'webpack.mix.js',
            'package' => 'package.json',
            'functions' => 'functions.php',
            'documentor' => 'phpdoc.dist.xml'
        ],
        'replacements' => [
            'webpack' => ['LOWER_NAME'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'scaffold/config' => ['STUDLY_NAME'],
            'providers/event' => ['MODULE_NAMESPACE','STUDLY_NAME'],
            'providers/auth' => ['MODULE_NAMESPACE','STUDLY_NAME'],
            'composer' => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
            ],
        ],
        'gitkeep' => false
    ],
    'public_path' => 'modules',
    'namespace' => 'Pingu',
    'composer' => [
        'vendor' => 'pingu',
        'author' => [
            'name' => 'Boris Blondin',
            'email' => 'blondin.boris@gmail.com',
        ]
    ],
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Set the generate key to false to not generate that folder
        */
        'generator' => [
            'settings' => ['path' => 'Config', 'generate' => false],
        ] 
    ],
];