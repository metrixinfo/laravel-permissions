<?php

namespace Metrix\LaravelPermissions\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Metrix\LaravelPermissions\Database\Factories\PermissionFactory;

/**
 * Permission Model
 *
 * @property int    $id
 * @property string $area
 * @property string $description
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Permission extends Model
{
    use HasFactory;

    public const PERMISSION_READ   = 1;
    public const PERMISSION_WRITE  = 2;
    public const PERMISSION_EDIT   = 4;
    public const PERMISSION_DELETE = 8;

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Guarded from mass assignments.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The model does not use timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Define attributes to have all
     * fields present when new'ing a model.
     *
     * @var array
     */
    protected $attributes = [
        'id'          => null,
        'area'        => null,
        'description' => null,
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return PermissionFactory::new();
    }

    /*
    |--------------------------------------------------------------------------
    | Model Relationships - Method names use snake_case
    |--------------------------------------------------------------------------
    */

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * A permission belongs to many roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')->withPivot('actions');
    }

    /**
     * A user can have many Permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions')->withPivot('actions');
    }

    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /*
    |--------------------------------------------------------------------------
    | Model Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Return a string representation of actions
     *
     * @param int|null $actions
     *
     * @return string
     */
    public static function actionsString(?int $actions): string
    {
        $permitted = [];

        if (Permission::PERMISSION_READ & $actions) {
            $permitted[] = 'Read';
        }

        if (Permission::PERMISSION_WRITE & $actions) {
            $permitted[] = 'Write';
        }

        if (Permission::PERMISSION_EDIT & $actions) {
            $permitted[] = 'Edit';
        }

        if (Permission::PERMISSION_DELETE & $actions) {
            $permitted[] = 'Delete';
        }

        return implode(', ', $permitted);
    }
}
