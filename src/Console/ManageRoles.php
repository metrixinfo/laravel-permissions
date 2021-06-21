<?php

namespace Metrix\LaravelPermissions\Console;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Metrix\LaravelPermissions\Models\Permission;
use Metrix\LaravelPermissions\Models\Role;

/**
 *  Create, Edit or Delete roles
 */
class ManageRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add, edit or delete roles';


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
                case 'List Roles':
                    $this->listRoles();
                    break;
                case 'Create Role':
                    $this->createRole();
                    break;
                case 'Edit Role':
                    $this->updateRole();
                    break;
                case 'Delete Role':
                    $this->deleteRole();
                    break;
                case 'Assign Role':
                    $this->assignRole();
                    break;
                case 'Revoke Role':
                    $this->revokeRole();
                    break;
                case 'Role Users':
                    $this->roleUsers();
                    break;
                case 'Role Permissions':
                    $this->rolePermissions();
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
                1 => 'List Roles',
                2 => 'Create Role',
                3 => 'Edit Role',
                4 => 'Delete Role',
                5 => 'Assign Role',
                6 => 'Revoke Role',
                7 => 'Role Users',
                8 => 'Role Permissions',
                0 => 'Quit',
            ],
            null,
            5,
            false
        );
    }

    /**
     * List all roles to a table
     *
     * @return void
     */
    private function listRoles(): void
    {
        $this->table(
            ['Id', 'Name', 'Description', 'Filter'],
            Role::all(['id', 'name', 'description', 'filter'])->toArray()
        );
    }

    /**
     * Create a new role.
     *
     * @return void
     */
    private function createRole(): void
    {
        $name = $this->ask('Role name?');
        $description = $this->ask('Role description?');

        try {
            $role = new Role();
            $role->name = $name;
            $role->description = $description;
            $result = $role->save();
            if (!$result) {
                $this->warn('Failed to create role.');
                return;
            }
        } catch (Exception $ex) {
            $this->error('Failed to create role: ' . chr(10) . $ex->getMessage());
        }

        $this->info('Role created.');
    }

    /**
     * Update a role.
     *
     * @return void
     */
    private function updateRole(): void
    {
        $id = $this->ask('Role ID to update?');

        $role = Role::find($id);
        if (!$role) {
            $this->warn('Failed to find role ' . $id . '.');
            return;
        }

        $name = $this->ask('New role name?', $role->name);
        $description = $this->ask('New role description?', $role->description);

        try {
            $role->name = trim($name);
            $role->description = trim($description);
            $result = $role->save();
            if (!$result) {
                $this->warn('Failed to update role.');
                return;
            }
        } catch (Exception $ex) {
            $this->error('Failed to update role: ' . chr(10) . $ex->getMessage());
        }

        $this->info('Role updated.');
    }

    /**
     * Delete a role by id.
     *
     * @return void
     */
    private function deleteRole(): void
    {
        $id = $this->ask('Role ID to delete?');
        $role = Role::find($id);
        if (!$role) {
            $this->warn('Failed to find role ' . $id);
            return;
        }

        try {
            $result = $role->delete();
            if (!$result) {
                $this->warn('Failed to delete role ' . $id);
                return;
            }
        } catch (Exception $ex) {
            $this->error('Failed to delete role ' . $id . chr(10) . $ex->getMessage());
        }

        $this->info('Role deleted.');
    }

    /**
     * Assign a role to a user.
     *
     * @return void
     */
    private function assignRole(): void
    {
        $id = $this->ask('Role ID to assign?');
        $role = Role::find($id);
        if (!$role) {
            $this->warn('Failed to find role ' . $id);
            return;
        }

        $id = $this->ask('User ID?');
        $user = User::find($id);
        if (!$user) {
            $this->warn('Failed to find user ' . $id . '.');
            return;
        }

        try {
            $user->roles()->attach($role->id);
        } catch (Exception $ex) {
            $this->warn('Failed to assign role.  - ' . $ex->getMessage());
            return;
        }

        $this->info('Role assigned.');
    }

    /**
     * Revoke a role to a user or a role.
     *
     * @return void
     */
    private function revokeRole(): void
    {
        $id = $this->ask('Role ID to revoke?');
        $role = Role::find($id);
        if (!$role) {
            $this->warn('Failed to find role ' . $id);
            return;
        }

        $id = $this->ask('User ID?');
        $user = User::find($id);
        if (!$user) {
            $this->warn('Failed to find user ' . $id . '.');
            return;
        }
        try {
            $user->roles()->detach($role->id);
        } catch (Exception $ex) {
            $this->warn('Failed to revoke role.  - ' . $ex->getMessage());
            return;
        }

        $this->info('Role revoked.');
    }

    /**
     * Display all the users assigned to a role.
     */
    private function roleUsers()
    {
        $id = $this->ask('Role ID?');
        $role = Role::find($id);
        if (!$role) {
            $this->warn('Failed to find role ' . $id . '.');
            return;
        }

        $this->info('Users belonging to role ' . $role->name);

        $table = [];
        foreach ($role->users as $user) {
            $table[] = ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
        }

        $this->table(
            ['Id', 'Name', 'Email'],
            $table
        );
    }

    /**
     * Display all permissions assigned to a role.
     */
    private function rolePermissions()
    {
        $id = $this->ask('Role ID?');
        $role = Role::find($id);
        if (!$role) {
            $this->warn('Failed to find role ' . $id . '.');
            return;
        }

        $this->info('Permissions assigned to role ' . $role->name);

        $table = [];
        foreach ($role->permissions as $permission) {
            $table[] = [
                'id' => $permission->id,
                'area' => $permission->area,
                'actions' => Permission::actionsString($permission->pivot->actions),
            ];
        }

        $this->table(
            ['Id', 'Area', 'Actions'],
            $table
        );
    }
}
