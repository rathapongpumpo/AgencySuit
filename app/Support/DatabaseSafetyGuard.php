<?php

namespace App\Support;

use RuntimeException;

final class DatabaseSafetyGuard
{
    public const PRODUCTION_HOST = '194.59.164.72';

    public const PRODUCTION_DATABASE = 'propagent';

    /** @var list<string> */
    private const DESTRUCTIVE_COMMANDS = [
        'db:wipe',
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'migrate:rollback',
    ];

    public static function assertSafeForTesting(?string $host, ?string $database): void
    {
        if (self::usesProductionDatabase($host, $database)) {
            throw new RuntimeException(
                'Database safety guard blocked automated tests: the configured database points to the production/shared database. Use a local/test database instead.'
            );
        }
    }

    public static function assertSafeForDestructiveCommand(string $command, ?string $host, ?string $database): void
    {
        if (in_array($command, self::DESTRUCTIVE_COMMANDS, true)
            && self::usesProductionDatabase($host, $database)) {
            throw new RuntimeException(
                "Database safety guard blocked {$command}: the configured database points to the production/shared database. Use a local/test database instead."
            );
        }
    }

    public static function usesProductionDatabase(?string $host, ?string $database): bool
    {
        return trim((string) $host) === self::PRODUCTION_HOST
            || strtolower(trim((string) $database)) === self::PRODUCTION_DATABASE;
    }
}
