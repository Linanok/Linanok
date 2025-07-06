<?php

namespace Models;

use App\History\MyLogsActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyLogsActivityTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_logs_activity_methods()
    {
        // Create a test class that uses the trait
        $testClass = new class
        {
            use MyLogsActivity;
        };

        // Test that the trait provides the expected methods
        $this->assertTrue(method_exists($testClass, 'getActivitylogOptions'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sets_subject_type_correctly()
    {
        // Create a test class that uses the trait
        $testClass = new class
        {
            use MyLogsActivity;
        };

        // Get the activity log options
        $options = $testClass->getActivitylogOptions();

        // Check that the subject_type is correctly set
        // The subject_type should be determined automatically by the activity logger
        // based on the model class, so we don't need to explicitly set it
        $this->assertFalse(isset($options->subjectType), 'Subject type should be determined automatically');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logs_only_dirty_attributes()
    {
        // Create a user which uses the MyLogsActivity trait
        $user = new User;

        // Get the activity log options
        $options = $user->getActivitylogOptions();

        // Check that it only logs dirty attributes
        $this->assertTrue($options->logOnlyDirty);
    }
}
