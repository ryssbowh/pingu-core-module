# Core module

## v2.0.0
- forms v2
- added helpers upload_max_filesize and temp_path
- added temp disk
- added storage link at installation
- added settings at installation
- added a ModuleServiceProvider as mother class
- added EditableModel and DeletableModel middlewares
- made abstract controllers
- moved themes in Themes folder and created sym link

## v1.1.4
- `AdminableContract` renamed to `HasAdminRoutes`
- `AjaxableContract` renamed to `HasAjaxRoutes`
- Contract `ModelController` renamed to `CreatesModelContract` and `EditModelContract`. Traits as well
- Contract `AjaxableModel` renamed to `EditsAjaxModelContract` and `DeletesAjaxModelContract`
- route slugs removed from base model and is now a contract/trait `HasRouteSlug`
- added stubs for event provider, auth provider, functions, exception, documentor
- modifed stubs for provider
- renamed start.php into functions.php in each module
- each module register their slugs through facade `ModelRoutes`
- added MakeException command
- added GenerateDoc command
- added phpDocumentor.phar
- removed TextSnippets
- added request as variable in `BaseController` constructor
- added `ProtectedModel`, `ParameterMissing`, `ModelSlugAlreadyRegistered` exceptions

## v1.1.3
- renamed api contracts/traits into ajax
- ajax calls with helpers throw events on body
- added permission 'browse site' to web routes
- removed SetAjaxThemeMiddleware
- moved web middleware in Core provider
- modified Theme::set

## v1.1.2 
- Refactored model controller
- made a adminable interface
- made HasChildren interface
- made HasItems interface
- made HasContextualLinks interface
- refactored model api controller
- Added theme config
- Added theme composers
- Added base npm dependencies into core and ignored them in merge-packages command
- Adaptation to Settings refactoring
- filling a basemodel field that is not fillable throws an exception
- debug bar as a middleware and subject to permission
- removed url segments, replaced by route slugs

## v1.1
- Fixed maintenance mode
- Refactored controllers
- added Routes/admin.php for core and new modules (stubs). Bound to 'access admin area' middleware.

## v1.1.1 Install script
## v1.0.14
- integrated permissions (seeder)
- fixed default module order
- fixed home middleware
- home page, admin home page
- admin route group
- improved base controller
- improved notify

## v1.0.9 Added themes as composer dependencies
## v1.0.5 More readme, added todo section
## v1.0.4 Wrote Readme

## TODO
- [x] Make admin home page
- [x] make composer install themes in proper folder automatically
- [x] write an install script
- [x] Include themes in merge-packages command
- [ ] Fix modules views publishing
- [ ] Maintenance mode switcher in back end
- [ ] Check theme extends
- [ ] Remake contextual links

### Facades available
- ContextualLinks
- ModelRoutes
- Notify
- Theme
- ThemeConfig
 
### Notify
Notify is a facade used to display messages to the user. it uses session to store them. see Components/Notify.php.
 
### Text Snippets
Removed
 
### Contextual Links
Contextual links are used to display links when viewing a model. The idea is to make it so that any page can have contextual links but at the moment is defined at model only. Your model must implements `HasContextualLinks`.
 
Will probably need rewritten as not the most intuitive way to use it.

### Routing
The contract `HasRouteSlugContract` and trait `hasRouteSlug` provides with a method to define a route slug for a model. If you want your model to be referenced in routes, you must extend this contract.

Every module at booting will register the slugs for all its models, duplicates will be checked.

### Admin routes
Provides a `HasAdminRoutes` interface and trait for models to define admin routes within the model. The trait provides with methods to replace variables in each route.

So you could define a `adminListItemsUri` in a model that define the route `content/{parent}/{item}/list`, retrieve it with `getAdminUri('listItems, true)`, and transform the uri with `transformAdminUri('listItems', [$parent, $item], true)`. The variables will be replaced by the route key name of each model. The boolean argument will prefix the route with the admin prefix (defined in config).
 
### Ajax
Provides a `HasAjaxRoutes` interface and trait for models to define ajax routes within the model. The trait provides with methods to replace variables in each route, works the exact same way as the admn routes, just the prefix changes.
 
Visible fields for an ajax request are set by the models $visible variable.

Helpers are available for ajax calls (get, post, put, \_delete, patch), each of those will throw 2 events on the body, `ajax.failed` and `ajax.success`.
 
### Controllers
Provides with 2 controllers to perform basic oprations on models :
- AdminModelController : add/edit/delete models. The controllers extending this controller must define `getModel()`. This model must implements the following contracts : `HasAdminRoutesContract` and `FormableContract`
- AjaxModelController : Same as above but for ajax calls. model must implement `HasAjaxRoutesContract` and `FormableContract`
 
### Middlewares
the `HomepageMiddleware` sets the homepage when the uri is /.
 
the `setThemeMiddleware` sets the current theme (if it's an ajax call, \_theme must be set in the call).

the `CheckFormaintenanceMode` does what its name says. /login will always be available. Users can use the site if they have the permission use site in maintenance mode'.

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
- removed default laravel view location, all views belong to a theme here
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
 
### stubs
Stubs are used to generate files when creating modules or themes.
 
### Assets
sass and js can be defined in any module or theme. packages.json can be defined in themes or modules. The base package.json is ignored by git, the Core one is responsible for its content.
 
When adding a library to a module's packages.json, you'll need to run the command `./artisan module:merge-packages` in order to merge them into a master packages.json at the root folder. Then you can run npm run watch. This way, when using `mix.extract` all the libraries will be in 2 separate files, vendor.js and manifest.js.
 
A good practice in js would be to have a file for each module to provide with functions that may be reused. The core module would have a core.js file that exports Core. Other modules can then import it with a `import Core from 'core';`

In order to have core available from other js files you'll need to add an alias in `webpack.mix.js` of your module :
`mix.webpackConfig({
  resolve: {
    alias: {
      'core': path.resolve(assetPath + '/js/components', './core')
    }
  }
});`
 
Core comes with helpers for ajax that may be reused. If you use those helpers the failed calls will be dumped in the console.
 
### Config
Config includes the admin and front theme, and the config used by [igaster/laravel-theme](https://github.com/igaster/laravel-theme)
 
### Schema less attributes
You'll find occurences of schema less attributes package, used to add attributes to models without changing the code of the model. This is promising but is not in use now.

### Maintenance mode
maintenance mode is to be set by command only.
default laravel middleware has been overwritten to allow /login to be accessible and to allow users with permissions 'use site in maintenance mode' to use the site normally.
Message, retry after and view defined in config.

### Debug bar
Debug bar from [https://github.com/barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar) is accessible if you have the permission `view debug bar`