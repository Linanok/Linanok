<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Database Compatibility Trait
 *
 * Provides database-agnostic methods for common operations that differ
 * between SQLite and PostgreSQL.
 */
trait DatabaseCompatible
{
    /**
     * Get the appropriate date truncation SQL for the current database driver.
     *
     * @param  string  $interval  The interval to truncate to (hour, day, week, month, year)
     * @param  string  $column  The column name to truncate (default: 'created_at')
     * @return string The SQL expression for date truncation
     */
    protected function getDateTruncSql(string $interval, string $column = 'created_at'): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql' => "date_trunc('$interval', $column)",
            'sqlite' => $this->getSqliteDateTruncSql($interval, $column),
            'mysql', 'mariadb' => $this->getMysqlDateTruncSql($interval, $column),
            'sqlsrv' => $this->getSqlServerDateTruncSql($interval, $column),
            default => $this->getSqliteDateTruncSql($interval, $column), // Default to SQLite format
        };
    }

    /**
     * Get SQLite-compatible date truncation SQL.
     */
    private function getSqliteDateTruncSql(string $interval, string $column): string
    {
        return match ($interval) {
            'hour' => "datetime(strftime('%Y-%m-%d %H:00:00', $column))",
            'day' => "date($column)",
            'week' => "date($column, 'weekday 0', '-6 days')",
            'month' => "date($column, 'start of month')",
            'year' => "date($column, 'start of year')",
            default => "date($column)",
        };
    }

    /**
     * Get MySQL-compatible date truncation SQL.
     */
    private function getMysqlDateTruncSql(string $interval, string $column): string
    {
        return match ($interval) {
            'hour' => "DATE_FORMAT($column, '%Y-%m-%d %H:00:00')",
            'day' => "DATE($column)",
            'week' => "DATE_SUB($column, INTERVAL WEEKDAY($column) DAY)",
            'month' => "DATE_FORMAT($column, '%Y-%m-01')",
            'year' => "DATE_FORMAT($column, '%Y-01-01')",
            default => "DATE($column)",
        };
    }

    /**
     * Get SQL Server-compatible date truncation SQL.
     */
    private function getSqlServerDateTruncSql(string $interval, string $column): string
    {
        return match ($interval) {
            'hour' => "DATEADD(hour, DATEDIFF(hour, 0, $column), 0)",
            'day' => "CAST($column AS DATE)",
            'week' => "DATEADD(week, DATEDIFF(week, 0, $column), 0)",
            'month' => "DATEADD(month, DATEDIFF(month, 0, $column), 0)",
            'year' => "DATEADD(year, DATEDIFF(year, 0, $column), 0)",
            default => "CAST($column AS DATE)",
        };
    }

    /**
     * Get the appropriate DISTINCT SQL for the current database driver.
     * Some databases handle DISTINCT differently with certain column types.
     */
    protected function getDistinctSql(string $column): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql' => "DISTINCT $column",
            'sqlite' => "DISTINCT $column",
            'mysql' => "DISTINCT $column",
            default => "DISTINCT $column",
        };
    }

    /**
     * Check if the current database driver supports a specific feature.
     */
    protected function databaseSupports(string $feature): bool
    {
        $driver = DB::connection()->getDriverName();

        return match ($feature) {
            'full_text_search' => in_array($driver, ['pgsql', 'mysql', 'mariadb', 'sqlsrv']),
            'json_operations' => in_array($driver, ['pgsql', 'mysql', 'mariadb', 'sqlite', 'sqlsrv']),
            'window_functions' => in_array($driver, ['pgsql', 'mysql', 'mariadb', 'sqlsrv']),
            'recursive_cte' => in_array($driver, ['pgsql', 'sqlite', 'sqlsrv']),
            'upsert' => in_array($driver, ['pgsql', 'mysql', 'mariadb', 'sqlite', 'sqlsrv']),
            'stored_procedures' => in_array($driver, ['pgsql', 'mysql', 'mariadb', 'sqlsrv']),
            'triggers' => in_array($driver, ['pgsql', 'mysql', 'mariadb', 'sqlite', 'sqlsrv']),
            'views' => in_array($driver, ['pgsql', 'mysql', 'mariadb', 'sqlite', 'sqlsrv']),
            default => false,
        };
    }

    /**
     * Get database-specific SQL for counting unique values.
     */
    protected function getCountDistinctSql(string $column): string
    {
        return "COUNT(DISTINCT $column)";
    }

    /**
     * Get database-specific SQL for case-insensitive LIKE operations.
     */
    protected function getILikeSql(string $column, string $value): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql' => "$column ILIKE '$value'",
            'sqlite' => "$column LIKE '$value' COLLATE NOCASE",
            'mysql', 'mariadb' => "$column LIKE '$value'", // MySQL/MariaDB are case-insensitive by default
            'sqlsrv' => "$column LIKE '$value' COLLATE SQL_Latin1_General_CP1_CI_AS",
            default => "LOWER($column) LIKE LOWER('$value')",
        };
    }
}
