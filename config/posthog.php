<?php

return [
    'enabled' => env('POSTHOG_ENABLED', false)
        && filled(env('POSTHOG_KEY'))
        && filled(env('POSTHOG_HOST')),
    'key' => env('POSTHOG_KEY'),
    'host' => rtrim((string) env('POSTHOG_HOST', ''), '/'),
    'session_replay_enabled' => env('POSTHOG_SESSION_REPLAY_ENABLED', false),
];
