<?php

namespace Pingu\Core\Listeners;

class DispatchThemeHook
{
    public function handle($event)
    {
        $identifier = $event->renderer->identifier();
        if ($object = $event->renderer->objectIdentifier()) {
            $object = $identifier.'_'.lcfirst(\Str::camel($object));
        }
        \ThemeHooks::dispatch(
            $identifier, 
            $object,
            $event->renderer->getHookData()
        );
    }
}