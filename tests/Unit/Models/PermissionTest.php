<?php

namespace Models;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_permission()
    {
        $permission = Permission::create([
            'name' => 'test permission',
            'guard_name' => 'web',
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'test permission',
            'guard_name' => 'web',
        ]);

        $this->assertEquals('test permission', $permission->name);
        $this->assertEquals('web', $permission->guard_name);
    }

    #[Test]
    public function it_can_be_converted_to_string()
    {
        $permission = Permission::create([
            'name' => 'test permission',
            'guard_name' => 'web',
        ]);

        $this->assertEquals('test permission', (string) $permission);
    }
}
