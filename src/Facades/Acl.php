<?php

namespace Metrix\LaravelPermissions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Metrix\LaravelPermissions\Skeleton\SkeletonClass
 */
class Acl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Metrix\LaravelPermissions\Acl::class;
    }
}
