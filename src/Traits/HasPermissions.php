<?php

namespace Metrix\LaravelPermissions\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Metrix\LaravelPermissions\Acl;
use Metrix\LaravelPermissions\Models\Permission;

/**
 * HasPermissions Trait
 */
trait HasPermissions
{
    /**
     * Acl instance
     *
     * @var mixed
     */
    protected $acl_instance;

    /*
    |--------------------------------------------------------------------------
    | Model Relationships - Method names use snake_case
    |--------------------------------------------------------------------------
    */

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * A user can have many permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withPivot('actions');
    }

    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /*
    |--------------------------------------------------------------------------
    | Model Methods - Method names use PascalCase
    |--------------------------------------------------------------------------
    */

    /**
     * Prepare a new or cached Acl instance
     *
     * @return mixed
     */
    public function acl()
    {
        if (! $this->acl_instance) {
            $this->acl_instance = app(Acl::class);
        }

        return $this->acl_instance;
    }
}
