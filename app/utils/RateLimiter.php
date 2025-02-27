<?php
class RateLimiter
{
    public static function check($key, $limit = 5, $window = 3600)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Start session only if not already active
        }
        $now = time();
        $requests = (isset($_SESSION[$key]) && is_array($_SESSION[$key])) ? $_SESSION[$key] : [];

        // Remove expired requests
        $requests = array_filter($requests, function ($t) use ($now, $window) {
            return $t > $now - $window;
        });

        if (count($requests) >= $limit) {
            return false;
        }

        $requests[] = $now;
        $_SESSION[$key] = $requests;
        return true;
    }
}
