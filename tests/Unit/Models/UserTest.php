<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_user(): void
    {
        // Arrange & Act
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    #[Test]
    public function it_hashes_the_password_when_creating_a_user(): void
    {
        // Arrange & Act
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        // Assert
        $this->assertNotEquals('password', $user->password);
    }

    #[Test]
    public function it_can_be_converted_to_string(): void
    {
        // Arrange & Act
        $user = User::factory()->create([
            'name' => 'Test User',
        ]);

        // Assert
        $this->assertEquals('Test User', (string) $user);
    }

    #[Test]
    public function it_has_roles(): void
    {
        // Arrange & Act
        $user = User::factory()->create();

        // Assert
        $this->assertTrue(method_exists($user, 'assignRole'));
        $this->assertTrue(method_exists($user, 'hasRole'));
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        // Arrange & Act
        $user = new User;

        // Assert
        $this->assertEquals([
            'name',
            'email',
            'password',
            'is_active',
            'is_super_admin',
        ], $user->getFillable());
    }

    #[Test]
    public function it_has_hidden_attributes(): void
    {
        // Arrange & Act
        $user = new User;

        // Assert
        $this->assertEquals([
            'password',
            'remember_token',
        ], $user->getHidden());
    }

    #[Test]
    public function it_has_password_cast_to_hashed(): void
    {
        // Arrange & Act
        $user = new User;
        $casts = $user->getCasts();

        // Assert
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('hashed', $casts['password']);
    }

    #[Test]
    public function it_has_logs_activity_trait(): void
    {
        // Arrange & Act
        $user = new User;

        // Assert
        $this->assertTrue(method_exists($user, 'getActivitylogOptions'));
    }
}
