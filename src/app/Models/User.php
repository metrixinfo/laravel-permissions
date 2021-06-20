<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Metrix\LaravelPermissions\Tests\Database\Factories\UserFactory;
use Metrix\LaravelPermissions\Traits\HasPermissions;
use Metrix\LaravelPermissions\Traits\HasRoles;

/**
 * Class User for testing only
 */
class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use HasFactory;
    use HasPermissions;
    use HasRoles;
    use Authorizable;
    use Authenticatable;

    /**
     * Guarded from mass assignments.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * @return \Metrix\LaravelPermissions\Tests\Database\Factories\UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
