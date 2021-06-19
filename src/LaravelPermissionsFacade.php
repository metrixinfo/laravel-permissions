<?php

namespace Metrix\LaravelPermissions;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Metrix\LaravelPermissions\Skeleton\SkeletonClass
 */
class LaravelPermissionsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-permissions';
    }
}
