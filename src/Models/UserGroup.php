<?php

namespace Metrix\LaravelPermissions\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Metrix\LaravelPermissions\Database\Factories\UserGroupFactory;

/**
 * Group - A user may belong to zero or one group.
 *
 * @property int    $id
 * @property string $name
 * @property string $description
 *
 * @property Collection $permissions
 * @property Collection $users
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class UserGroup extends Model
{
    use HasFactory;

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'user_groups';

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
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return UserGroupFactory::new();
    }

    /*
    |--------------------------------------------------------------------------
    | Model Relationships - Method names use snake_case
    |--------------------------------------------------------------------------
    */

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * A UserGroup has many Users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * A group can have many Permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
}
