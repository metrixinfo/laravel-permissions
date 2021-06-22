<?php

namespace Metrix\LaravelPermissions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Metrix\LaravelPermissions\Models\Permission;

/**
 * Class LaravelPermissions
 *
 * @package Metrix\LaravelPermissions
 */
class Acl
{
    /**
     * @var bool
     */
    private bool $booted = false;

    /**
     * @var int|null
     */
    private ?int $user_id;

    /**
     * @var string
     */
    private string $session_id;

    /**
     * @var array
     */
    private array $permissions = [];

    /**
     * @var array
     */
    private array $merged_permissions = [];

    /**
     * @var array
     */
    private array $user_permissions = [];

    /**
     * @var array
     */
    private array $roles = [];

    /**
     * @var array;
     */
    private array $role_permissions = [];

    /**
     * @var string|null;
     */
    private ?string $filter = null;

    /**
     * @var string
     */
    private string $cache_key;

    /**
     * @var int;
     */
    private int $cache_ttl = 86400;

    /**
     * @var bool
     */
    private bool $cache_tagging = true;


    /**
     * Acl constructor.
     */
    public function __construct()
    {
        $this->cache_ttl = \config('permissions.cache_ttl', 86400);
        $this->cache_tagging = \config('permissions.cache_tagging', true);
    }

    /**
     * Boot the class by fetching the users permissions
     * from Cache or DB if not cached.
     *
     * @param $user_id
     *
     * @return void
     */
    public function boot($user_id = null): void
    {
        $this->user_id = $user_id;

        $this->session_id = Session::getId();

        // No user, nothing permitted.
        if ($this->user_id === null) {
            $this->filter = 'D';
            return;
        }

        $this->cache_key = 'acl:' . $this->user_id . ':' . $this->session_id;

        $permissions = $this->getCachedPermissions();
        if (! $permissions) {
            $this->refreshPermissions();
        }

        $this->booted = true;
    }

    /**
     * Load the cached permissions, if they are not available or invalid return false.
     *
     * @return bool
     */
    private function getCachedPermissions(): bool
    {
        if ($this->cache_tagging) {
            $serial_permissions = Cache::tags(['acl', 'acl:' . $this->user_id])->get($this->cache_key);
        } else {
            $serial_permissions = Cache::get($this->cache_key);
        }

        if (! $serial_permissions) {
            return false;
        }

        $permissions = \unserialize($serial_permissions, [false]);
        if ($permissions === false) {
            return false;
        }

        if (! \is_array($permissions)) {
            return false;
        }

        if (! array_key_exists('merged_permissions', $permissions)) {
            return false;
        }

        if (! array_key_exists('filter', $permissions)) {
            return false;
        }

        $this->merged_permissions = $permissions['merged_permissions'] ?? [];
        $this->filter             = $permissions['filter'] ?? null;
        $this->roles              = $permissions['roles'] ?? [];

        return true;
    }

    /**
     * Refresh the users permissions and insert them into the cache.
     *
     * @return void
     */
    public function refreshPermissions(): void
    {
        $this->getPermissions();
        $this->getUserRoles();
        $this->getRolePermissions();
        $this->getUserPermissions();
        $this->mergePermissions();

        $permissions = [
            'merged_permissions' => $this->merged_permissions,
            'filter'             => $this->filter,
            'roles'              => $this->roles,
        ];

        if ($this->cache_tagging) {
            Cache::tags(['acl', 'acl:' . $this->user_id])->put($this->cache_key, \serialize($permissions), $this->cache_ttl);
        } else {
            Cache::put($this->cache_key, \serialize($permissions), $this->cache_ttl);
        }
    }

    /**
     * Get all the permission to be able
     * to map areas to permissions.
     *
     * @return void
     */
    private function getPermissions(): void
    {
        $results = DB::table('permissions')
            ->select('id', 'area')
            ->get();

        /** @var \Metrix\LaravelPermissions\Models\Permission $permission */
        foreach ($results as $permission) {
            $this->permissions[ $permission->id ] = [
                'area' => $permission->area,
            ];
        }
    }

    /**
     * Retrieve the users permissions from the Db.
     *
     * @return void
     */
    private function getUserPermissions(): void
    {
        $results = DB::table('permission_user')
            ->select('permission_id', 'actions')
            ->where('user_id', $this->user_id)
            ->get();

        foreach ($results as $permission) {
            $this->user_permissions[ $permission->permission_id ] = $permission->actions;
        }
    }

    /**
     * Retrieve the users role permissions from the Db.
     *
     * @return void
     */
    private function getRolePermissions(): void
    {
        $results = DB::table('permission_role')
            ->select('permission_id', 'actions')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('role_user')
                    ->where('user_id', '=', $this->user_id);
            })->get();

        foreach ($results as $permission) {
            $this->role_permissions[ $permission->permission_id ] = $permission->actions;
        }
    }

    /**
     * Retrieve the users roles from the Db.
     *
     * @return void
     */
    private function getUserRoles(): void
    {
        $results = DB::table('role_user')
            ->select('roles.id', 'roles.name', 'roles.filter')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $this->user_id)
            ->orderBy('roles.id', 'desc')
            ->get();

        /** @var \Metrix\LaravelPermissions\Models\Role $role */
        foreach ($results as $role) {
            $this->roles[ $role->id ] = [ 'name' => $role->name, 'filter' => $role->filter ];
            if ($role->filter !== null) {
                $this->filter = $role->filter;
            }
        }
    }

    /**
     * Merge the permission in a format that can be used for looking up
     * if the user has a permission for an area.
     *
     * @return void
     */
    private function mergePermissions(): void
    {
        // Get the role permissions
        // $key = the permission id | $value is the user_permission actions
        foreach ($this->role_permissions as $key => $value) {
            if (isset($this->permissions[ $key ])) {
                $this->merged_permissions[ $this->permissions[ $key ]['area'] ] = $value;
            }
        }

        // Merge in the user permissions. They are additive only.
        foreach ($this->user_permissions as $key => $value) {
            if (isset($this->permissions[ $key ])) {
                if (isset($this->merged_permissions[ $this->permissions[ $key ]['area'] ])) {
                    $this->merged_permissions[ $this->permissions[ $key ]['area'] ] |= $value;
                } else {
                    $this->merged_permissions[ $this->permissions[ $key ]['area'] ] = $value;
                }
            }
        }
    }

    /**
     * Verify if the user has a permission
     *
     * @param string $area
     * @param int    $action
     *
     * @return bool
     */
    public function hasPermission($area = null, $action = null): bool
    {
        if ($this->filter !== null) {
            return $this->filterPermission();
        }

        if ($area === null || $action === null || $this->booted === false) {
            return false;
        }

        if (isset($this->merged_permissions[ $area ])) {
            return $action & $this->merged_permissions[$area];
        }

        return false;
    }

    /**
     * Verify if the user has READ permission
     *
     * @param string $area
     *
     * @return bool
     */
    public function hasRead($area = null): bool
    {
        if ($this->filter !== null) {
            return $this->filterPermission();
        }

        if ($area === null || $this->booted === false) {
            return false;
        }

        if (isset($this->merged_permissions[ $area ])) {
            return Permission::PERMISSION_READ & $this->merged_permissions[$area];
        }

        return false;
    }

    /**
     * Verify if the user has WRITE permission
     *
     * @param string $area
     *
     * @return bool
     */
    public function hasWrite($area = null): bool
    {
        if ($this->filter !== null) {
            return $this->filterPermission();
        }

        if ($area === null || $this->booted === false) {
            return false;
        }

        if (isset($this->merged_permissions[ $area ])) {
            return Permission::PERMISSION_WRITE & $this->merged_permissions[$area];
        }

        return false;
    }

    /**
     * Verify if the user has EDIT permission
     *
     * @param string $area
     *
     * @return bool
     */
    public function hasEdit($area = null): bool
    {
        if ($this->filter !== null) {
            return $this->filterPermission();
        }

        if ($area === null || $this->booted === false) {
            return false;
        }

        if (isset($this->merged_permissions[ $area ])) {
            return Permission::PERMISSION_EDIT & $this->merged_permissions[$area];
        }

        return false;
    }

    /**
     * Verify if the user has DELETE permission
     *
     * @param string $area
     *
     * @return bool
     */
    public function hasDelete($area = null): bool
    {
        if ($this->filter !== null) {
            return $this->filterPermission();
        }

        if ($area === null || $this->booted === false) {
            return false;
        }

        if (isset($this->merged_permissions[ $area ])) {
            return Permission::PERMISSION_DELETE & $this->merged_permissions[ $area ];
        }

        return false;
    }

    /**
     * Verify if the user has a role
     *
     * @param int $role_id
     *
     * @return bool
     */
    public function hasRole($role_id): bool
    {
        return \array_key_exists($role_id, $this->roles);
    }

    /**
     * Return permission based on role filter.
     *
     * @return bool
     */
    private function filterPermission(): bool
    {
        if ($this->filter === 'A') {
            return true;
        }

        if ($this->filter === 'D') {
            return false;
        }

        Log::warning('Unknown role filter ' . $this->filter);
        return false;
    }
}
