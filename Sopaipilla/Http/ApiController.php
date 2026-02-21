<?php

declare(strict_types=1);

namespace Sopaipilla\Http;

use Sopaipilla\Validation\ValidationException;
use Sopaipilla\Validation\Dto;
use Sopaipilla\Security\Security;

abstract class ApiController
{
    /** Allowed origins. '*' = any. Restrict in production. */
    protected static string $allowedOrigin = '*';

    public function __construct()
    {
        self::sendSecurityHeaders();
    }

    private static function sendSecurityHeaders(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: default-src \'none\'');

        // CORS
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (static::$allowedOrigin === '*') {
            header('Access-Control-Allow-Origin: *');
        } elseif ($origin === static::$allowedOrigin) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    protected function json(mixed $data, int $status = 200): string
    {
        http_response_code($status);
        $payload = ['success' => true, ...(is_array($data) ? $data : ['data' => $data])];
        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        return $encoded;
    }

    protected function error(string $message, int $status = 400): string
    {
        http_response_code($status);
        $encoded = json_encode(
            ['success' => false, 'error' => $message],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        return $encoded;
    }

    /** Responds with the created resource (201) or a 500 error on failure. */
    protected function okOr201(mixed $data, string $message = 'Processing error'): string
    {
        return $data
            ? $this->json(['data' => $data], 201)
            : $this->error($message, 500);
    }

    /** Responds with the resource or a 404 error if falsy. */
    protected function okOr404(mixed $data, string $message = 'Not found'): string
    {
        return $data
            ? $this->json(['data' => $data])
            : $this->error($message, 404);
    }

    /** 422 response with all validation errors indexed by field. */
    protected function validationError(ValidationException $e): string
    {
        http_response_code(422);
        $encoded = json_encode(
            ['success' => false, 'errors' => $e->getErrors()],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        return $encoded;
    }

    /**
     * Reads and sanitizes the JSON request body.
     * Returns null if the body is invalid, empty, or exceeds $maxBytes.
     */
    protected function input(int $maxBytes = 1_048_576): ?array
    {
        return Security::jsonInput($maxBytes);
    }

    /**
     * Builds a DTO and executes the callback if validation passes.
     * If the input is invalid or validation fails, returns the error response directly.
     *
     * Usage:
     *   return $this->withDto(CreateUserDTO::class, function(CreateUserDTO $dto) {
     *       return $this->json(['data' => ...]);
     *   });
     */
    protected function withDto(string $dtoClass, callable $callback): string
    {
        $input = Security::jsonInput();

        if ($input === null) {
            return $this->error('Invalid or empty JSON body', 400);
        }

        try {
            $dto = $dtoClass::from($input);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        }

        return $callback($dto);
    }

    /**
     * @deprecated Use withDto() instead.
     */
    protected function fromDto(string $dtoClass, ?array $input, ?string &$errorResponse = null): ?Dto
    {
        if ($input === null) {
            $errorResponse = $this->error('Invalid or empty JSON body', 400);
            return null;
        }

        try {
            return $dtoClass::from($input);
        } catch (ValidationException $e) {
            $errorResponse = $this->validationError($e);
            return null;
        }
    }
}
