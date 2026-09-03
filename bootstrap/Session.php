<?php

namespace Bootstrap;
class Session {
    static function handle() {
        session_save_path(__DIR__.'/../'.config('session_path'));
        session_start([
            'cookie_lifetime' => config('session_lifetime'),
        ]);
    }
}
