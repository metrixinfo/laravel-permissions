<?php

namespace Metrix\LaravelPermissions\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Metrix\LaravelPermissions\Models\Role;

/**
 * HasRoles Trait
 */
trait HasRoles
{
    /*
    |--------------------------------------------------------------------------
    | Model Relationships - Method names use snake_case
    |--------------------------------------------------------------------------
    */

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * A user may have many roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
}
