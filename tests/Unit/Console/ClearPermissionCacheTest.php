<?php
/*
 *   phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */

namespace Metrix\LaravelPermissions\Tests\Unit\Console;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Metrix\LaravelPermissions\Models\Permission;
use Metrix\LaravelPermissions\Tests\TestCase;

/**
 * Class PermissionUserTest
 */
class ClearPermissionCacheTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * @return void
     * @throws Exception
     */
    public function it_can_clear_a_specific_users_permissions(): void
    {
        $perm = Permission::factory()->create();
        $user = User::factory()->create();
        $user->permissions()->attach($perm->id, ['actions' => 7]);
        $this->assertDatabaseHas('permission_user', [
            'permission_id' => $perm->id,
            'user_id' => $user->id,
            'actions' => 7,
        ]);

        $acl = app(\Metrix\LaravelPermissions\Acl::class);
        $acl->boot($user->id);

        $session_id = Session::getId();
        $cached = Cache::tags(['acl', 'acl:' . $user->id])->get('acl:' . $user->id . ':' . $session_id);
        self::assertNotNull($cached);

        $this->artisan('acl:clear')
            ->expectsConfirmation('This will delete the cached permission for ALL users. Are you sure?', 'no');

        $cached = Cache::tags(['acl', 'acl:' . $user->id])->get('acl:' . $user->id . ':' . $session_id);
        self::assertNotNull($cached);

        $this->artisan('acl:clear -u ' . $user->id);
        $cached = Cache::tags(['acl', 'acl:' . $user->id])->get('acl:' . $user->id . ':' . $session_id);
        self::assertNull($cached);
    }

    /**
     * @test
     *
     * @return void
     * @throws Exception
     */
    public function it_can_clear_all_user_permissions(): void
    {
        $perm = Permission::factory()->create();
        $user1 = User::factory()->create();
        $user1->permissions()->attach($perm->id, ['actions' => 7]);
        $this->assertDatabaseHas('permission_user', [
            'permission_id' => $perm->id,
            'user_id' => $user1->id,
            'actions' => 7,
        ]);

        $user2 = User::factory()->create();
        $user2->permissions()->attach($perm->id, ['actions' => 15]);
        $this->assertDatabaseHas('permission_user', [
            'permission_id' => $perm->id,
            'user_id' => $user2->id,
            'actions' => 15,
        ]);

        $acl = app(\Metrix\LaravelPermissions\Acl::class);
        $acl->boot($user1->id);
        $acl->boot($user2->id);

        $session_id = Session::getId();
        $cached = Cache::tags(['acl', 'acl:' . $user1->id])->get('acl:' . $user1->id . ':' . $session_id);
        self::assertNotNull($cached);

        $cached = Cache::tags(['acl', 'acl:' . $user2->id])->get('acl:' . $user2->id . ':' . $session_id);
        self::assertNotNull($cached);

        $this->artisan('acl:clear')
            ->expectsConfirmation('This will delete the cached permission for ALL users. Are you sure?', 'yes');

        $cached = Cache::tags(['acl', 'acl:' . $user1->id])->get('acl:' . $user1->id . ':' . $session_id);
        self::assertNull($cached);

        $cached = Cache::tags(['acl', 'acl:' . $user2->id])->get('acl:' . $user2->id . ':' . $session_id);
        self::assertNull($cached);
    }
}
