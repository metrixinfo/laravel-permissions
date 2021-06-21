<?php

namespace Metrix\LaravelPermissions\Console;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Metrix\LaravelPermissions\Models\Permission;
use Metrix\LaravelPermissions\Models\Role;

/**
 *  Create, Edit or Delete permissions
 */
class ManagePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add, edit or delete permissions';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $action = null;

        while ($action !== 'Quit') {
            $action = $this->showMenu();
            switch ($action) {
                case 'List Permissions':
                    $this->listPermissions();
                    break;
                case 'Create Permission':
                    $this->createPermission();
                    break;
                case 'Edit Permission':
                    $this->updatePermission();
                    break;
                case 'Delete Permission':
                    $this->deletePermission();
                    break;
                case 'Assign Permission':
                    $this->assignPermission();
                    break;
                case 'Revoke Permission':
                    $this->revokePermission();
                    break;
                case 'Audit Permissions':
                    $this->auditPermissions();
                    break;
            }
        }
    }

    /**
     * @return string
     */
    private function showMenu(): string
    {
        return $this->choice(
            'What would you like to do?',
            [
                1 => 'List Permissions',
                2 => 'Create Permission',
                3 => 'Edit Permission',
                4 => 'Delete Permission',
                5 => 'Assign Permission',
                6 => 'Revoke Permission',
                7 => 'Audit Permissions',
                0 => 'Quit',
            ],
            null,
            5,
            false
        );
    }

    /**
     * @return string
     */
    private function roleOrUser(): string
    {
        return $this->choice(
            'For a role or a user?',
            [
                1 => 'Role',
                2 => 'User',
            ],
            'Role',
            5,
            false
        );
    }


    /**
     * List out all permissions in a table
     *
     * @return void
     */
    private function listPermissions(): void
    {
        $this->table(
            ['Id', 'Area', 'Description'],
            Permission::all(['id', 'area', 'description'])->toArray()
        );
    }

    /**
     * Create a new permission.
     *
     * @return void
     */
    private function createPermission(): void
    {
        $area = $this->ask('What is the permission area?');
        $description = $this->ask('What is the permission description?');

        try {
            $permission = new Permission();
            $permission->area = $area;
            $permission->description = $description;
            $result = $permission->save();
            if (!$result) {
                $this->warn('Failed to create permission.');
                return;
            }
        } catch (Exception $ex) {
            $this->error('Failed to create permission: ' . chr(10) . $ex->getMessage());
        }

        $this->info('Permission created.');
    }

    /**
     * Update a permission.
     *
     * @return void
     */
    private function updatePermission(): void
    {
        $id = $this->ask('Permission ID to update?');

        $permission = Permission::find($id);
        if (!$permission) {
            $this->warn('Failed to find permission ' . $id . '.');
            return;
        }

        $area = $this->ask('New permission area?', $permission->area);
        $description = $this->ask('New permission description?', $permission->description);

        try {
            $permission->area = trim($area);
            $permission->description = trim($description);
            $result = $permission->save();
            if (!$result) {
                $this->warn('Failed to update permission.');
                return;
            }
        } catch (Exception $ex) {
            $this->error('Failed to update permission: ' . chr(10) . $ex->getMessage());
        }

        $this->info('Permission updated.');
    }

    /**
     * Delete a permission by id.
     *
     * @return void
     */
    private function deletePermission(): void
    {
        $id = $this->ask('Permission ID to delete?');
        $permission = Permission::find($id);
        if (!$permission) {
            $this->warn('Failed to find permission ' . $id);
            return;
        }

        try {
            $result = $permission->delete();
            if (!$result) {
                $this->warn('Failed to delete permission ' . $id);
                return;
            }
        } catch (Exception $ex) {
            $this->error('Failed to delete permission ' . $id . chr(10) . $ex->getMessage());
        }

        $this->info('Permission deleted.');
    }

    /**
     * Assign a permission to a user or a role.
     *
     * @return void
     */
    private function assignPermission(): void
    {
        $id = $this->ask('Permission ID to assign?');
        $permission = Permission::find($id);
        if (!$permission) {
            $this->warn('Failed to find permission ' . $id);
            return;
        }

        $type = $this->roleOrUser();

        switch ($type) {
            case 'Role':
                $id = $this->ask('Role ID?');
                $role = Role::find($id);
                if (!$role) {
                    $this->warn('Failed to find role ' . $id . '.');
                    return;
                }
                try {
                    $role->permissions()->attach($permission->id);
                } catch (Exception $ex) {
                    $this->warn('Failed to assign permission.  - ' . $ex->getMessage());
                    return;
                }
                break;
            case 'User':
                $id = $this->ask('User ID?');
                $user = User::find($id);
                if (!$user) {
                    $this->warn('Failed to find user ' . $id . '.');
                    return;
                }
                try {
                    $user->permissions()->attach($permission->id);
                } catch (Exception $ex) {
                    $this->warn('Failed to assign permission.  - ' . $ex->getMessage());
                    return;
                }
                break;
            default:
                $this->error('Failed to determine whom to assign the permission to.');
                return;
        }

        $this->info('Permission assigned.');
    }

    /**
     * Revoke a permission to a user or a role.
     *
     * @return void
     */
    private function revokePermission(): void
    {
        $id = $this->ask('Permission ID to revoke?');
        $permission = Permission::find($id);
        if (!$permission) {
            $this->warn('Failed to find permission ' . $id);
            return;
        }

        $type = $this->roleOrUser();

        switch ($type) {
            case 'Role':
                $id = $this->ask('Role ID?');
                $role = Role::find($id);
                if (!$role) {
                    $this->warn('Failed to find role ' . $id . '.');
                    return;
                }
                try {
                    $role->permissions()->detach($permission->id);
                } catch (Exception $ex) {
                    $this->warn('Failed to revoke permission.  - ' . $ex->getMessage());
                    return;
                }
                break;
            case 'User':
                $id = $this->ask('User ID?');
                $user = User::find($id);
                if (!$user) {
                    $this->warn('Failed to find user ' . $id . '.');
                    return;
                }
                try {
                    $user->permissions()->detach($permission->id);
                } catch (Exception $ex) {
                    $this->warn('Failed to revoke permission.  - ' . $ex->getMessage());
                    return;
                }
                break;
            default:
                $this->error('Failed to determine whom to detach the permission from.');
                return;
        }

        $this->info('Permission revoked.');
    }

    /**
     * List all the permissions for a user or a role.
     *
     * @return void
     */
    private function auditPermissions(): void
    {
        $type = $this->roleOrUser();

        switch ($type) {
            case 'Role':
                $this->rolePermissions();
                break;
            case 'User':
                $this->userPermissions();
                break;
            default:
                $this->error('Failed to determine whom to audit permissions.');
                return;
        }
    }

    /**
     * Display all the permissions assigned to a role.
     */
    private function rolePermissions()
    {
        $id = $this->ask('Role ID?');
        $role = Role::find($id);
        if (!$role) {
            $this->warn('Failed to find role ' . $id . '.');
            return;
        }

        $this->info('Permissions for role ' . $role->name);

        $table = [];
        foreach ($role->permissions as $permission) {
            $table[] = [
                'id' => $permission->id,
                'area' => $permission->area,
                'action' => Permission::actionsString($permission->pivot->actions),
            ];
        }

        $this->table(
            ['Id', 'Area', 'Action'],
            $table
        );
    }

    /**
     * Display all the permissions assigned to a user.
     */
    private function userPermissions()
    {
        $id = $this->ask('User ID?');
        $user = User::find($id);
        if (!$user) {
            $this->warn('Failed to find user ' . $id . '.');
            return;
        }

        $this->info('Permissions for user ' . $user->name);

        $table = [];
        foreach ($user->permissions as $permission) {
            $table[$permission->id] = [
                'id' => $permission->id,
                'area' => $permission->area,
                'action' => Permission::actionsString($permission->pivot->actions),
            ];
        }

        $roles = $user->roles;
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $table[$permission->id] = [
                    'id' => $permission->id,
                    'area' => $permission->area,
                    'action' => Permission::actionsString($permission->pivot->actions),
                ];
            }
        }

        $this->table(
            ['Id', 'Area', 'Action'],
            $table
        );
    }
}
