<?php

declare(strict_types=1);

namespace Sopaipilla;

/**
 * Minimal .env file loader.
 *
 * Parses KEY=VALUE pairs from a .env file and exposes them via:
 *   - $_ENV superglobal
 *   - getenv()
 *   - Env::get()
 *
 * Supported syntax:
 *   APP_NAME=MyApp          # bare value
 *   DB_PASSWORD="secret"    # double-quoted (quotes stripped)
 *   DB_PASSWORD='secret'    # single-quoted (quotes stripped)
 *   # This is a comment     # ignored
 *   EMPTY_VAR=              # results in empty string
 *
 * Already-set environment variables are NOT overwritten (safe for production
 * where env vars may be injected by the web server or container runtime).
 */
class Env
{
    /** @var array<string, string> In-memory copy of all loaded variables. */
    private static array $vars = [];

    /**
     * Load a .env file into the environment.
     * Silently does nothing if the file does not exist.
     *
     * @param string $path Absolute path to the .env file.
     */
    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(ltrim($line), '#')) {
                continue;
            }

            // Must contain '='
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key   = trim($key);
            $value = trim($value);

            // Strip surrounding quotes
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            // Strip inline comments on unquoted values (e.g. VALUE=foo # comment)
            if (!str_starts_with($value, '"') && !str_starts_with($value, "'")) {
                $value = trim(explode(' #', $value, 2)[0]);
            }

            // Do not overwrite existing environment variables
            if (getenv($key) !== false) {
                continue;
            }

            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;

            self::$vars[$key] = $value;
        }
    }

    /**
     * Get an environment variable by key.
     *
     * @param string      $key     Variable name.
     * @param string|null $default Value returned when the key is not set.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? getenv($key);

        return $value !== false && $value !== null ? (string) $value : $default;
    }

    /**
     * Get an environment variable and throw if it is not set.
     *
     * @throws \RuntimeException when the key is missing.
     */
    public static function require(string $key): string
    {
        $value = static::get($key);

        if ($value === null) {
            throw new \RuntimeException("Missing required environment variable: {$key}");
        }

        return $value;
    }

    /** Return all variables loaded from the .env file (excludes pre-existing env vars). */
    public static function all(): array
    {
        return self::$vars;
    }
}
