<?php

namespace Metrix\LaravelPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Roles - Users can belong to many roles.
 *
 * @property int    $id
 * @property string $name
 * @property string $description
 * @property string $filter
 *
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Role extends Model
{
    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Guarded from mass assignments.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * Define attributes to have fields
     * present when new'ing a model.
     *
     * @var array
     */
    protected $attributes = [
        'id'          => null,
        'name'        => null,
        'description' => null,
        'filter'      => null,
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Relationships - Method names use snake_case
    |--------------------------------------------------------------------------
    */

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * The users that belong to the role.
     *
     * @return BelongsToMany
     *
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /**
     * A role can have many Permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')->withPivot('actions');
    }

    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
}
