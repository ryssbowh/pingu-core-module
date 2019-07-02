<?php
namespace Pingu\Core\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Pingu\Core\Components\ThemeViewFinder;
use Pingu\Core\Console\{createTheme, refreshThemeCache, listThemes};

class ThemeServiceProvider extends ServiceProvider
{

    public function register()
    {
        /*--------------------------------------------------------------------------
        | Bind in IOC
        |--------------------------------------------------------------------------*/
        $this->app->singleton('core.themes', function () {
            return new \Pingu\Core\Components\Themes();
        });

        /*--------------------------------------------------------------------------
        | Replace FileViewFinder
        |--------------------------------------------------------------------------*/
        $this->app->singleton('view.finder', function ($app) {
            $finder = new ThemeViewFinder(
                $app['files'],
                $app['config']['view.paths'],
                null
            );
            \View::setFinder($finder);
            return $finder;
        });

    }

    public function boot(Router $router)
    {
        /*--------------------------------------------------------------------------
        | Initialize Themes
        |--------------------------------------------------------------------------*/
        $themes = $this->app->make('core.themes');
        $themes->scanThemes();

        /*--------------------------------------------------------------------------
        | Register Console Commands
        |--------------------------------------------------------------------------*/
        $this->commands([
            listThemes::class,
            createTheme::class,
            refreshThemeCache::class,
        ]);

        /*--------------------------------------------------------------------------
        | Register custom Blade Directives
        |--------------------------------------------------------------------------*/

        $this->registerBladeDirectives();
    }

    protected function registerBladeDirectives()
    {
        /*--------------------------------------------------------------------------
        | Extend Blade to support Orcherstra\Asset (Asset Managment)
        |
        | Syntax:
        |
        |   @css (filename, alias, depends-on-alias)
        |   @js  (filename, alias, depends-on-alias)
        |--------------------------------------------------------------------------*/

        Blade::extend(function ($value) {
            return preg_replace_callback('/\@js\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/', function ($match) {

                $p1 = trim($match[1], " \t\n\r\0\x0B\"'");
                $p2 = trim(empty($match[2]) ? $p1 : $match[2], " \t\n\r\0\x0B\"'");
                $p3 = trim(empty($match[3]) ? '' : $match[3], " \t\n\r\0\x0B\"'");

                if (empty($p3)) {
                    return "<?php Asset::script('$p2', theme_url('$p1'));?>";
                } else {
                    return "<?php Asset::script('$p2', theme_url('$p1'), '$p3');?>";
                }

            }, $value);
        });

        Blade::extend(function ($value) {
            return preg_replace_callback('/\@jsIn\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/',
                function ($match) {

                    $p1 = trim($match[1], " \t\n\r\0\x0B\"'");
                    $p2 = trim($match[2], " \t\n\r\0\x0B\"'");
                    $p3 = trim(empty($match[3]) ? $p2 : $match[3], " \t\n\r\0\x0B\"'");
                    $p4 = trim(empty($match[4]) ? '' : $match[4], " \t\n\r\0\x0B\"'");

                    if (empty($p4)) {
                        return "<?php Asset::container('$p1')->script('$p3', theme_url('$p2'));?>";
                    } else {
                        return "<?php Asset::container('$p1')->script('$p3', theme_url('$p2'), '$p4');?>";
                    }

                }, $value);
        });

        Blade::extend(function ($value) {
            return preg_replace_callback('/\@css\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/', function ($match) {

                $p1 = trim($match[1], " \t\n\r\0\x0B\"'");
                $p2 = trim(empty($match[2]) ? $p1 : $match[2], " \t\n\r\0\x0B\"'");
                $p3 = trim(empty($match[3]) ? '' : $match[3], " \t\n\r\0\x0B\"'");

                if (empty($p3)) {
                    return "<?php Asset::style('$p2', theme_url('$p1'));?>";
                } else {
                    return "<?php Asset::style('$p2', theme_url('$p1'), '$p3');?>";
                }

            }, $value);
        });
    }

}

