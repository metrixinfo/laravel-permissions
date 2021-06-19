<?php
/*
 *   phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
*/

namespace Metrix\LaravelPermissions\Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Metrix\LaravelPermissions\Models\Permission;

/**
 * Class PermissionUserTest
 */
class PermissionUserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * @return void
     */
    public function it_can_assign_a_permission_to_a_user(): void
    {
        $perm = Permission::factory()->create();
        $user = User::factory()->create();
        $user->permissions()->attach($perm->id, ['actions' => 7]);
        $this->assertDatabaseHas('permission_user', [
            'permission_id' => $perm->id,
            'user_id' => $user->id,
            'actions' => 7,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_allow_or_deny_a_permission_to_a_user(): void
    {
        $perm = Permission::factory()->create();
        $user = User::factory()->create();
        $user->permissions()->attach($perm->id, ['actions' => Permission::PERMISSION_READ | Permission::PERMISSION_WRITE]);
        $user->acl()->boot($user->id);

        $result = $user->acl()->hasRead($perm->area);
        self::assertTrue($result);

        $result = $user->acl()->hasWrite($perm->area);
        self::assertTrue($result);

        $result = $user->acl()->hasEdit($perm->area);
        self::assertFalse($result);

        $result = $user->acl()->hasDelete($perm->area);
        self::assertFalse($result);
    }
}
