<?php

namespace Pingu\Core\Listeners;

class DispatchThemeHook
{
    public function handle($event)
    {
        \ThemeHooks::dispatch($event->renderer->identifier(), $event->renderer->getHookData());
    }   
}