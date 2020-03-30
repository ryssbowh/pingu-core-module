<?php

namespace Pingu\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Pingu\Core\Events\Rendering;
use Pingu\Core\Listeners\DispatchThemeHook;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Rendering::class => [
            DispatchThemeHook::class
        ]
    ];
}