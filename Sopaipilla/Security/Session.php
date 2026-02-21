<?php

declare(strict_types=1);

namespace Sopaipilla\Security;

/**
 * Thin wrapper around PHP's native session functions.
 *
 * - Enforces secure cookie flags (httponly, samesite=Lax) before starting
 * - Guards against double-start with a static flag
 * - Provides a flash() helper for one-time read-and-destroy values
 */
class Session
{
    /** Tracks whether session_start() has already been called in this request. */
    private static bool $started = false;

    /** Configure secure cookie params and start the session (idempotent). */
    private static function start(): void
    {
        if (self::$started || session_status() !== PHP_SESSION_NONE) {
            self::$started = true;
            return;
        }

        // Set secure cookie attributes before starting the session
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,             // Inaccessible from JavaScript
            'samesite' => 'Lax',            // Basic CSRF protection
        ]);

        session_start();
        self::$started = true;
    }

    /** Store a value in the session under the given key. */
    public static function create(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /** Remove a value from the session. */
    public static function destroy(string $key): void
    {
        self::start();
        $_SESSION[$key] = null;
        unset($_SESSION[$key]);
    }

    /** Read a value from the session; returns null if the key does not exist. */
    public static function read(string $key): mixed
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    /** Return true if the key exists and is non-empty. */
    public static function exists(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]) && !empty($_SESSION[$key]);
    }

    /**
     * Get-or-set a flash value (read-once).
     *
     * - Called with $value: stores the value and returns null.
     * - Called without $value: reads the value, deletes it, and returns it.
     */
    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            self::create($key, $value);
            return null;
        }

        $value = self::read($key);
        self::destroy($key);
        return $value;
    }

    /** Regenerate the session ID to prevent session fixation attacks. */
    public static function regenerate(): bool
    {
        self::start();
        return session_regenerate_id(true);
    }
}
