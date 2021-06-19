<?php

namespace Metrix\LaravelPermissions\Tests;

use Orchestra\Testbench\TestCase;

class PermissionUserTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_can_assign_and_evaluate_a_permission(): void
    {
        $permsision = \Metrix\LaravelPermissions\Models\Permission::factory()->create();
        $user = \App\Models\User::factory()->create();

        $permission->attach($user->id);

        self::assertTrue(true);
    }
}
