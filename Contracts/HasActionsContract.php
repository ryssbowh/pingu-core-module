<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Contracts\ActionRepositoryContract;

interface HasActionsContract
{
    /**
     * Registered instance for this model
     * 
     * @return Actions
     */
    public static function actions(): ActionRepositoryContract;
}