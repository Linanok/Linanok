<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;

class UserPolicyTestCase extends BasePolicyTestCase
{
    protected function setUp(): void
    {
        $this->modelName = 'user';
        $this->policyClass = UserPolicy::class;
        $this->model = new User;

        parent::setUp();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_update_their_own_profile()
    {
        // Create a test user
        $testUser = User::factory()->create();

        // Set the authenticated user
        $this->actingAs($testUser);

        // User should be able to update their own profile
        $this->assertTrue($testUser->can('update', $testUser));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_update_another_users_profile_without_permission()
    {
        // Create two test users
        $testUser1 = User::factory()->create();
        $testUser2 = User::factory()->create();

        // Set the authenticated user
        $this->actingAs($testUser1);

        // User should not be able to update another user's profile without permission
        $this->assertFalse($testUser1->can('update', $testUser2));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_with_permission_can_update_another_users_profile()
    {
        // Create a test user with permission
        $testUser = User::factory()->create();
        $testUser->givePermissionTo('update user');

        // Create another user
        $anotherUser = User::factory()->create();

        // Set the authenticated user
        $this->actingAs($testUser);

        // User with permission should be able to update another user's profile
        $this->assertTrue($testUser->can('update', $anotherUser));
    }
}
