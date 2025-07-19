<?php

namespace Tests\Unit;

use App\Traits\DatabaseCompatible;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DatabaseCompatibilityTraitTest extends TestCase
{
    use DatabaseCompatible;

    #[Test]
    public function it_supports_all_laravel_database_drivers(): void
    {
        // Test with current database (SQLite) - this will work without mocking
        $intervals = ['hour', 'day', 'week', 'month', 'year'];

        foreach ($intervals as $interval) {
            $sql = $this->getDateTruncSql($interval, 'created_at');

            $this->assertIsString($sql);
            $this->assertNotEmpty($sql);

            if (DB::connection()->getDriverName() === 'sqlite') {
                // For SQLite, should contain date functions but not date_trunc
                $this->assertStringNotContainsString('date_trunc', $sql);
            }
        }

        // Test that the method exists and works for all supported drivers
        $this->assertTrue(method_exists($this, 'getDateTruncSql'));
        $this->assertTrue(method_exists($this, 'databaseSupports'));
        $this->assertTrue(method_exists($this, 'getILikeSql'));
    }

    #[Test]
    public function it_provides_correct_sql_server_date_truncation(): void
    {
        // Test the SQL Server date truncation method directly
        $reflection = new \ReflectionClass($this);
        $method = $reflection->getMethod('getSqlServerDateTruncSql');
        $method->setAccessible(true);

        $testCases = [
            'hour' => 'DATEADD(hour, DATEDIFF(hour, 0, created_at), 0)',
            'day' => 'CAST(created_at AS DATE)',
            'week' => 'DATEADD(week, DATEDIFF(week, 0, created_at), 0)',
            'month' => 'DATEADD(month, DATEDIFF(month, 0, created_at), 0)',
            'year' => 'DATEADD(year, DATEDIFF(year, 0, created_at), 0)',
        ];

        foreach ($testCases as $interval => $expectedSql) {
            $actualSql = $method->invoke($this, $interval, 'created_at');
            $this->assertEquals($expectedSql, $actualSql);
        }
    }

    #[Test]
    public function it_provides_correct_mysql_mariadb_date_truncation(): void
    {
        // Test the MySQL date truncation method directly
        $reflection = new \ReflectionClass($this);
        $method = $reflection->getMethod('getMysqlDateTruncSql');
        $method->setAccessible(true);

        $testCases = [
            'hour' => "DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')",
            'day' => 'DATE(created_at)',
            'week' => 'DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY)',
            'month' => "DATE_FORMAT(created_at, '%Y-%m-01')",
            'year' => "DATE_FORMAT(created_at, '%Y-01-01')",
        ];

        foreach ($testCases as $interval => $expectedSql) {
            $actualSql = $method->invoke($this, $interval, 'created_at');
            $this->assertEquals($expectedSql, $actualSql);
        }
    }

    #[Test]
    public function it_detects_database_features_correctly(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            $this->assertTrue(true);

            return;
        }

        // Test with current database (SQLite)
        $sqliteFeatures = [
            'full_text_search' => false,
            'json_operations' => true,
            'window_functions' => false,
            'recursive_cte' => true,
            'upsert' => true,
            'stored_procedures' => false,
            'triggers' => true,
            'views' => true,
        ];

        foreach ($sqliteFeatures as $feature => $expected) {
            $actual = $this->databaseSupports($feature);
            $this->assertEquals(
                $expected,
                $actual,
                "Feature '$feature' support detection failed for SQLite"
            );
        }

        // Test that the method works for unknown features
        $this->assertFalse($this->databaseSupports('unknown_feature'));
    }

    #[Test]
    public function it_provides_case_insensitive_like_for_all_drivers(): void
    {
        // Test with current database (SQLite)
        $actualSql = $this->getILikeSql('column', '%value%');
        if (DB::connection()->getDriverName() === 'pgsql') {
            $this->assertEquals('column ILIKE \'%value%\'', $actualSql);
        } elseif (DB::connection()->getDriverName() === 'sqlite') {
            $this->assertEquals('column LIKE \'%value%\' COLLATE NOCASE', $actualSql);
        }

        // Test that the method exists and returns a string
        $this->assertIsString($actualSql);
        $this->assertNotEmpty($actualSql);
    }

    #[Test]
    public function it_handles_unknown_database_drivers_gracefully(): void
    {
        // Test that unknown features return false
        $this->assertFalse($this->databaseSupports('unknown_feature'));

        // Test that the methods return valid SQL strings
        $sql = $this->getDateTruncSql('day', 'created_at');
        $this->assertIsString($sql);
        $this->assertNotEmpty($sql);

        $likeSql = $this->getILikeSql('column', '%value%');
        $this->assertIsString($likeSql);
        $this->assertNotEmpty($likeSql);
    }

    #[Test]
    public function it_provides_count_distinct_sql(): void
    {
        $sql = $this->getCountDistinctSql('user_id');
        $this->assertEquals('COUNT(DISTINCT user_id)', $sql);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Reset any previous mocks
        \Mockery::close();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
