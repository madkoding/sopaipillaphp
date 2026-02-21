<?php

declare(strict_types=1);

namespace Sopaipilla\Security;

/**
 * Input sanitization and request hardening.
 *
 * - Strips XSS vectors from all superglobals on boot (cleanAll)
 * - Provides a safe JSON body reader (jsonInput)
 * - Blocks disallowed HTTP methods and null-byte attacks
 */
class Security
{
    /** XSS patterns: scripts, inline events, dangerous URIs, embedding tags */
    private static array $labels = [
        // <script> blocks
        '@<script[^>]*?>.*?</script>@si',
        // Inline events: onclick, onerror, onload, etc.
        '@\s+on\w+\s*=\s*["\'][^"\']*["\']@si',
        '@\s+on\w+\s*=\s*[^\s>]+@si',
        // Dangerous URIs
        '@href\s*=\s*["\']?\s*javascript:[^"\'> \s]*@si',
        '@src\s*=\s*["\']?\s*data:[^"\'> \s]*@si',
        // Embedding tags (iframes, forms, objects, etc.)
        '@<(iframe|object|embed|applet|form|base)[^>]*>.*?</\1>@si',
        '@<(iframe|object|embed|applet|form|base)[^>]*/?>@si',
        // SVG with event handlers
        '@<svg[^>]*>.*?</svg>@si',
        // Numeric entities
        '@&#(\d+);@',
        // Legacy template engines and shortcodes
        '@\[\[(.*?)\]\]@si',
        '@\[!(.*?)!\]@si',
        '@\[\~(.*?)\~\]@si',
        '@\[\((.*?)\)\]@si',
        '@{{(.*?)}}@si',
        '@\[\+(.*?)\+\]@si',
        '@\[\*(.*?)\*\]@si',
    ];

    private static array $allowedMethods = [
        'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS',
    ];

    public static function cleanAll(): void
    {
        // Null byte in query string — refuse early
        if (isset($_SERVER['QUERY_STRING']) && str_contains(urldecode($_SERVER['QUERY_STRING']), "\0")) {
            http_response_code(400);
            exit('Bad Request');
        }

        // Disallowed HTTP method
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
        if (!in_array($method, self::$allowedMethods, true)) {
            http_response_code(405);
            header('Allow: ' . implode(', ', self::$allowedMethods));
            exit('Method Not Allowed');
        }

        self::cleanAllVars();
    }

    /**
     * Sanitizes and decodes the JSON request body.
     * @param int $maxBytes Body size limit (default 1 MB)
     */
    public static function jsonInput(int $maxBytes = 1_048_576): ?array
    {
        $raw = file_get_contents('php://input', length: $maxBytes);

        if ($raw === false || $raw === '') {
            return null;
        }

        // Null bytes in body are a smuggling indicator — reject the request
        if (str_contains($raw, "\0")) {
            return null;
        }

        $data = json_decode($raw, associative: true, depth: 16);

        if (!is_array($data)) {
            return null;
        }

        return self::clean($data);
    }

    /**
     * Trim a string or recursively trim an array of strings.
     * stripslashes is intentionally omitted — magic_quotes_gpc was removed in PHP 5.4.
     */
    public static function sanitize(array|string $value = ''): array|string
    {
        // stripslashes not needed: magic_quotes_gpc has not existed since PHP 5.4
        return is_array($value)
            ? array_map(self::sanitize(...), $value)
            : trim($value);
    }

    /**
     * Recursively strip XSS patterns from an array up to $limit levels deep.
     * Strings are scrubbed using the regex patterns in $labels.
     */
    private static function clean(array $target, int $limit = 5): array
    {
        foreach ($target as $key => $value) {
            if (is_array($value) && $limit > 0) {
                $target[$key] = self::clean($value, $limit - 1);
            } elseif (is_string($value)) {
                $target[$key] = preg_replace_callback(
                    self::$labels,
                    fn(): string => '',
                    $value
                );
            }
        }
        return $target;
    }

    /**
     * Sanitize all standard superglobals ($_GET, $_POST, $_COOKIE, $_REQUEST)
     * and escape the most commonly spoofed $_SERVER keys.
     */
    private static function cleanAllVars(): void
    {
        foreach ([&$_GET, &$_POST, &$_COOKIE, &$_REQUEST] as &$eachClean) {
            $eachClean = self::clean($eachClean);
            $eachClean = self::sanitize($eachClean);
        }

        foreach (['PHP_SELF', 'HTTP_USER_AGENT', 'HTTP_REFERER', 'QUERY_STRING'] as $key) {
            $_SERVER[$key] = isset($_SERVER[$key])
                ? htmlspecialchars($_SERVER[$key], ENT_QUOTES, 'UTF-8')
                : null;
        }
    }
}
