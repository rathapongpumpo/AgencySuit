<?php

namespace App\Providers;

use App\Support\DatabaseSafetyGuard;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $connection = config('database.default');
        $host = config("database.connections.{$connection}.host");
        $database = config("database.connections.{$connection}.database");

        if ($this->app->runningUnitTests()) {
            DatabaseSafetyGuard::assertSafeForTesting($host, $database);
        }

        Event::listen(CommandStarting::class, function (CommandStarting $event) use ($host, $database): void {
            DatabaseSafetyGuard::assertSafeForDestructiveCommand($event->command, $host, $database);
        });
    }
}
