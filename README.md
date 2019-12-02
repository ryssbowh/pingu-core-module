# Core module

## TODO
- [x] Make admin home page
- [x] make composer install themes in proper folder automatically
- [x] write an install script
- [x] Include themes in merge-packages command
- [ ] Fix modules views publishing
- [x] Maintenance mode switcher in back end
- [ ] Check theme extends
- [x] Remake contextual links

### Facades available
- ContextualLinks
- ModelRoutes
- Notify
- Theme
- ThemeConfig
- JsConfig
- ArrayCache
- Actions
- Policies
- Routes
- Uris
- Settings

### Uris

Facade to register Uris class for objects

### Routes

Facade to register Routes class for objects and register them in LAravel Route system

### Actions

Facade to register Actions class for objects

### Policies

Facade to register Policy class for objects and to register them in Laravel Gate system
 
### Notify
Notify is a facade used to display messages to the user. it uses session to store them. see Components/Notify.php.

### Settings
Settings is used to save config variables in database so they can be edited through the back end.
Settings are defined in the application through repositories.

Repositories must extend `SettingsRepository`, they define permissions, fields, titles etc and must be registered in the `Settings` facade in the register method of a service provider

Settings uses cache which is emptied every time a setting is saved.

A setting can be encrypted in database.

Settings throw events before a setting is changed and after it's saved.

Seeder may use the create method of repositories to create the values in database : `\Settings::get('general')->create()`
 
### Contextual Links
Contextual links are used to display links when viewing a model. The idea is to make it so that any page can have contextual links but at the moment is defined at model only. Your model must implements `HasContextualLinks`.
 
Will probably need rewritten as not the most intuitive way to use it.
 
### Ajax
Visible fields for an ajax request are set by the models $visible variable.

Helpers are available for ajax calls (get, post, put, \_delete, patch), each of those will throw 2 events on the body, `ajax.failed` and `ajax.success`.
 
### Controllers
Provide with a `BaseController` that has helper method to access route actions and route parameters
 
### Middlewares
the `HomepageMiddleware` sets the homepage when the uri is /.
 
the `setThemeMiddleware` sets the current theme (if it's an ajax call, \_theme must be set in the call).

the `ActivateDebugBar` activates the debug bar if the right permission is set.

`redirectIfAuthenticated` used on routes that are only for non-authenticated users.

the `EditableModel` will check if a model is editable, the model must have a field editable. call it with `editableModel:{modelSlug}`

the `DeletableModel` will check if a model is deletable, the model must have a field deletable. call it with `deletableModel:{modelSlug}`
 
### Base model
The base model **which all models must extend** provides with friendly names and static route keys methods.
 
### functions
The functions.php provides with a couple of useful functions.
 
### Themes
The code from [igaster/laravel-theme](https://github.com/igaster/laravel-theme) has been ported over.
Changes to it includes :
- rewrote findNamespacedView of themeViewFinder
- Themes can define a config which will override the normal config. Access it with `theme_config()` which will return the normal config if it does not exists in your theme.
- Themes can define Composers to add variables to any view. use the command `module:make-composer`.
- Admin theme will be set if request starts with /admin or if ajax call define a \_theme=admin
- Themes now sits in Themes folder. a symbolic link is created at theme creation. It links public/themes/{themeName} to Themes/{themeName}/public so you can have assets publicly available in your Themes folder. If your running your site in a vagrant it's important to run the create command from within your box, or the link will be incorrect. To access your assets you can use the `theme_url` function.
 
### Commands
Includes commands provided by [igaster/laravel-theme](https://github.com/igaster/laravel-theme) from which packaging commands have been removed.
 
- The module:merge-packages will look at the base packages.json and all modules packages.json and merge them. option to auto resolve conflicts.
- the module:generate-doc will generate the docs for one or all modules with phpDocumentor, in the docs/ folder
- the module:make-exception generate an exception in the Exceptions/ folder
- the theme:make-composer generates a new composer for a theme, in the Composers\ folder.
- build-assets will rebuild all the assets
- make-composer will create a composer for a theme
- module:link will rebuild the sym links for the modules, if you run your site in a vagrant this **must** be called from within the box
- theme:link same as above but for themes
- db:seed : Has been rewritten to account for migratable seedings
- make:seed : Has been rewritten to account for migratable seedings
- db:seed-rollback : Rolls back seedings in a folder (default database/seeds)

### Migratable seedings
[https://github.com/eighty8/laravel-seeder](https://github.com/eighty8/laravel-seeder) has been ported over (not all the environment part of it) to provide with migratable seedings that can be rolled back.
 
### stubs
Stubs are used to generate files when creating modules or themes.
 
### Assets
sass and js can be defined in any module or theme. packages.json can be defined in themes or modules. The base package.json is ignored by git, the package-base.json one is responsible for defining the npm scripts.
 
When adding a library to a module's packages.json, you'll need to run the command `npm run installAll` in order to merge them into a master packages.json at the root folder and install the libraries. Running `npm run dev` or `npm run production` will also run this command first. 
This way, when using `mix.extract` all the libraries will be in 2 separate files, vendor.js and manifest.js.
 
A good practice in js would be to have a file for each module to provide with functions that may be reused. The core module would have a core.js file that exports Core. Other modules can then import it with a `import Core from 'core';`

In order to have a library (let's say core) available from other js files you'll need to add an alias in `webpack.mix.js` of your module :
`mix.webpackConfig({
  resolve: {
    alias: {
      'core': path.resolve(assetPath + '/js/components', './core')
    }
  }
});`
 
Core comes with helpers for ajax that may be reused (import * as h from 'PinguHelpers'). If you use those helpers the failed calls will be dumped in the console.
The h.log() function does the same as console.log except that it will not log in production mode.
config accessible by h.config or by importing the Config object (import Config from 'PinguConfig')
 
### Config
Config includes the admin and front theme, and the config used by [igaster/laravel-theme](https://github.com/igaster/laravel-theme)

### Js Config
Config can be sent to the front end by registering it through the facade JsConfig.
 
### Schema less attributes
You'll find occurences of schema less attributes package, used to add attributes to models without changing the code of the model. This is promising but is not in use now.

### Debug bar
Debug bar from [https://github.com/barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar) is accessible if you have the permission `view debug bar`

### Database Blueprint
The Blueprint class has been extended to include `updatedBy`, `createdBy` and `deletedBy` methods that adds a field to a table : `updated_by`, `created_by` and `deleted_by` which are all a foreign key to the table users.

if using `deletedBy`, you must also use laravel `softDeletes` or it won't work.

### ArrayCache

This is a helper to save cache looking at keys as dotted arrays. So we are able to clear any sub-array we want. example :
I have a cache `fields.object1.fields` and `fields.object1.validator`, Array Cache will empty all cache for object1 if you call `ArrayCache::forget('fields.object1')` and will empty all cache for fields if you call `ArrayCache::forget('fields')` 

### Core Modules boot order
Core modules :
- Core : -100
- Field : -99
- Entity : -99
- User : -98
- Permissions : -97
- Block : -96
- Menu : -95
- Forms : -60
- Content: -50
- Media : -40
- Page : -30
- Taxonomy : -20