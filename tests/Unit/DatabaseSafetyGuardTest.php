<?php

namespace Tests\Unit;

use App\Support\DatabaseSafetyGuard;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class DatabaseSafetyGuardTest extends TestCase
{
    #[Test]
    public function safe_local_test_database_is_allowed(): void
    {
        DatabaseSafetyGuard::assertSafeForTesting('127.0.0.1', 'agencysuit_test');

        $this->assertTrue(true);
    }

    #[Test]
    public function production_host_blocks_automated_tests(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database safety guard blocked automated tests');

        DatabaseSafetyGuard::assertSafeForTesting(DatabaseSafetyGuard::PRODUCTION_HOST, 'agencysuit_test');
    }

    #[Test]
    public function production_database_name_blocks_automated_tests(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database safety guard blocked automated tests');

        DatabaseSafetyGuard::assertSafeForTesting('127.0.0.1', DatabaseSafetyGuard::PRODUCTION_DATABASE);
    }

    #[Test]
    public function destructive_command_is_blocked_for_a_production_database(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database safety guard blocked migrate:fresh');

        DatabaseSafetyGuard::assertSafeForDestructiveCommand('migrate:fresh', '127.0.0.1', DatabaseSafetyGuard::PRODUCTION_DATABASE);
    }
}
