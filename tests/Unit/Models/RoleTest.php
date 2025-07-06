<?php

namespace Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_role()
    {
        $role = Role::create([
            'name' => 'Test Role',
            'guard_name' => 'web',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
            'guard_name' => 'web',
        ]);

        $this->assertEquals('Test Role', $role->name);
        $this->assertEquals('web', $role->guard_name);
    }

    #[Test]
    public function it_can_be_converted_to_string()
    {
        $role = Role::create([
            'name' => 'Test Role',
            'guard_name' => 'web',
        ]);

        $this->assertEquals('Test Role', (string) $role);
    }

    #[Test]
    public function it_can_be_assigned_permissions()
    {
        $role = Role::create([
            'name' => 'Test Role',
            'guard_name' => 'web',
        ]);

        $permission = Permission::create([
            'name' => 'test permission',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo($permission);

        $this->assertTrue($role->hasPermissionTo($permission));
    }

    #[Test]
    public function it_can_be_assigned_to_users()
    {
        $role = Role::create([
            'name' => 'Test Role',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create();

        $user->assignRole($role);

        $this->assertTrue($user->hasRole($role));
    }
}
