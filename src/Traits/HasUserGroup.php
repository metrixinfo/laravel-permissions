<?php

namespace Metrix\LaravelPermissions\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Metrix\LaravelPermissions\Models\UserGroup;

/**
 * HasGroup Trait
 */
trait HasUserGroup
{
    /*
    |--------------------------------------------------------------------------
    | Model Relationships - Method names use snake_case
    |--------------------------------------------------------------------------
    */

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * A user may belong to a group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
}
