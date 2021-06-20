<?php

namespace Metrix\LaravelPermissions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Metrix\LaravelPermissions\Database\Factories\RoleFactory;

/**
 * Roles - Users can belong to many roles.
 *
 * @property int    $id
 * @property string $name
 * @property string $description
 * @property string $filter
 *
 * @property Collection $permissions
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Role extends Model
{
    use HasFactory;

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
     * The model does not use timestamps
     *
     * @var bool
     */
    public $timestamps = false;

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

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }

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
        return $this->belongsToMany(Permission::class)->withPivot('actions');
    }

    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
}
