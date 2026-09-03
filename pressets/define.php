<?php

return [
    'session_path' => env('SESSION_STORAGE') ?? "/sessions",
    'session_lifetime' => env('SESSION_LIFETIME') ?? 2*60*60,

    'route' => [
    ]
];
