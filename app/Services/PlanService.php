<?php

namespace App\Services;

use App\Models\User;

class PlanService
{
    public function plan(User $user): string
    {
        return array_key_exists((string) $user->plan, config('plans', []))
            ? (string) $user->plan
            : 'free';
    }

    public function limit(User $user, string $key): ?int
    {
        $limit = config("plans.{$this->plan($user)}.limits.{$key}");

        return $limit === null ? null : (int) $limit;
    }

    public function reached(User $user, string $key, int $current): bool
    {
        $limit = $this->limit($user, $key);

        return $limit !== null && $current >= $limit;
    }
}
