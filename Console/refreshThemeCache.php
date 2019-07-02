<?php 
namespace Pingu\Core\Console;

use Pingu\Core\Facades\Theme;

class refreshThemeCache extends baseThemeCommand
{
    protected $signature = 'theme:refresh-cache';
    protected $description = 'Rebuilds the cache of "theme.json" files for each theme';

    public function handle()
    {
        // Rebuild Themes Cache
        Theme::rebuildCache();

        $this->info("Themes cache was refreshed. Currently theme caching is: " . (Theme::cacheEnabled() ? "ENABLED" : "DISABLED"));
    }

}
