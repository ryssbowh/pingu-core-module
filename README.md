# Core module

## v1.1
- Fixed maintenance mode
- Refactored controllers
- added Routes/admin.php for core and new modules (stubs). Bound to 'access admin area' middleware.

## v1.0.5 More readme, added todo section
## v1.0.4 Wrote Readme
 
## TODO
- [x] Make admin home page
- [x] make composer install themes in proper folder automatically
- [ ] write an install script
- [ ] Include themes in merge-packages command
- [ ] Fix modules views publishing
- [ ] Maintenance mode switcher in back end
 
### Notify
Notify is a facade used to display messages to the user. it uses session to store them. see Components/Notify.php.
 
### Text Snippets
That was a test but is not used atm.
 
### Contextual Links
Contextual links are used to display links when viewing a model. The idea is to make it so that any page can have contextual links but at the moment is defined at model only.
 
Will probably need rewritten as not the most intuitive way to use it.
 
### Api
Provides a contract to make a model Apiable, this contract only has one method for now (apiUrl).
 
Visible fields for an api request are set by the models $visible variable.
 
### Controllers
Provides with an api controller for models that handles some basic operation on models.
 
Provides with an model controller that allows creating/editing through a form.
 
Any controller can implements ModelController Contract and define a `getModel` method. The model must implement `FormableModel`. The rest is done automatically (given that you have defined your fields for your model)
 
### Middlewares
the HomepageMiddleware sets the homepage when the uri is /.
 
the setApiMiddleware sets the theme for an api call (theme is set by the call through a \_isAdmin variable).
 
the setThemeMiddleware sets the current theme. If the url starts with /admin the theme will be the admin theme defined in config.
 
### Base model
The base model **which all models must extend** provides with methods for url segments and route slugs, as well as a friendly name methods that are used often.
 
### functions
The start.php provides with a couple of useful functions.
 
### Themes
The code from [igaster/laravel-theme](https://github.com/igaster/laravel-theme) has been ported over.
Changes to it includes :
- removed default laravel view location, all views belong to a theme here
- rewrote findNamespacedView of themeViewFinder
 
### Commands
Includes commands provided by [igaster/laravel-theme](https://github.com/igaster/laravel-theme) from which packaging commands have been removed.
 
The core:merge-packages will look at the base packages.json and all modules packages.json and merge them. option to auto resolve conflicts.
 
### stubs
Stubs are used to generate files when creating modules or themes.
 
### Assets
sass and js can be define in any module or theme. packages.json can be defined in modules.
@todo extend this functionnality to themes.
 
When adding a library to a module's packages.json, you'll need to run the command `./artisan core:merge-packages` in order to merge them into a master packages.json at the root folder. Then you can run npm run watch. This way, when using `mix.extract` all the libraries will be in 2 separate files, vendor.js and manifest.js.
 
A good practice in js would be to have a file for each module to provide with functions that may be reused. The core module would have a core.js file that exports Core. Other modules can then import it with a `import Core from 'core';`
 
Core comes with helpers for ajax that may be reused.
 
### Config
Config includes the admin and front theme, and the config used by [igaster/laravel-theme](https://github.com/igaster/laravel-theme)
 
### Schema less attributes
You'll find occurences of schema less attributes package, used to add attributes to models without changing the code of the model. This is promising but is not in use now.

### Maintenance mode
maintenance mode is to be set by command only.
default laravel middleware has been overwritten to allow /login to be accessible and to allow users with permissions 'use site in maintenance mode' to use the site normally.
Message, retry after and view defined in config.