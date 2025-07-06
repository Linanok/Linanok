<?php

namespace Tests\Unit\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

abstract class BasePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $regularUser;

    protected User $userWithPermission;

    protected string $modelName;

    protected string $policyClass;

    protected Model $model;

    #[Test]
    public function super_admin_can_view_any(): void
    {
        $this->assertTrue($this->superAdmin->can('viewAny', $this->model));
    }

    #[Test]
    public function super_admin_can_view(): void
    {
        $this->assertTrue($this->superAdmin->can('view', $this->model));
    }

    #[Test]
    public function super_admin_can_create(): void
    {
        $this->assertTrue($this->superAdmin->can('create', $this->model));
    }

    #[Test]
    public function super_admin_can_update(): void
    {
        $this->assertTrue($this->superAdmin->can('update', $this->model));
    }

    #[Test]
    public function super_admin_can_delete(): void
    {
        $this->assertTrue($this->superAdmin->can('delete', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function regular_user_can_view_any_with_permission()
    {
        $this->assertTrue($this->regularUser->can('viewAny', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function regular_user_can_view_with_permission()
    {
        $this->assertTrue($this->regularUser->can('view', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function regular_user_cannot_create_without_permission()
    {
        $this->assertFalse($this->regularUser->can('create', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function regular_user_cannot_update_without_permission()
    {
        $this->assertFalse($this->regularUser->can('update', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function regular_user_cannot_delete_without_permission()
    {
        $this->assertFalse($this->regularUser->can('delete', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_with_permission_can_view_any()
    {
        $this->assertTrue($this->userWithPermission->can('viewAny', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_with_permission_can_view()
    {
        $this->assertTrue($this->userWithPermission->can('view', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_with_permission_can_create()
    {
        $this->assertTrue($this->userWithPermission->can('create', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_with_permission_can_update()
    {
        $this->assertTrue($this->userWithPermission->can('update', $this->model));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_with_permission_can_delete()
    {
        $this->assertTrue($this->userWithPermission->can('delete', $this->model));
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $regularRole = Role::create(['name' => 'Regular User']);

        // Create permissions for the model being tested
        Permission::create(['name' => "view {$this->modelName}"]);
        Permission::create(['name' => "create {$this->modelName}"]);
        Permission::create(['name' => "update {$this->modelName}"]);
        Permission::create(['name' => "delete {$this->modelName}"]);

        // Assign permissions to regular role
        $regularRole->givePermissionTo("view {$this->modelName}");

        // Create users
        $this->superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'is_super_admin' => true,
        ]);
        $this->regularUser = User::factory()->create(['name' => 'Regular User']);
        $this->userWithPermission = User::factory()->create(['name' => 'User With Permission']);

        // Assign roles to the regular user
        $this->regularUser->assignRole('Regular User');

        // Give specific permissions to the user with permission
        $this->userWithPermission->givePermissionTo([
            "view {$this->modelName}",
            "create {$this->modelName}",
            "update {$this->modelName}",
            "delete {$this->modelName}",
        ]);
    }
}
