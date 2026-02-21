<?php

declare(strict_types=1);

namespace Sopaipilla\Security;

use Exception;
use RuntimeException;

/**
 * Symmetric encryption, password hashing and token generation.
 *
 * Encryption  : AES-256-GCM (authenticated cipher — prevents padding oracle and bit-flipping)
 * Hashing     : Argon2ID via password_hash()
 * Token gen   : CSPRNG via random_bytes()
 *
 * Requires the RANDOM_SEED constant to be defined before any encrypt/decrypt call.
 */
class Crypt
{
    /** AES-256-GCM: authenticated encryption. Prevents padding oracle and bit-flipping. */
    private static string $cipher    = 'aes-256-gcm';
    /** Authentication tag length in bytes (128-bit tag). */
    private static int    $tagLength = 16;

    /**
     * Encrypt a plain-text string.
     * Returns a base64-encoded payload in the format: iv | tag | ciphertext
     */
    public static function encrypt(string $text): string
    {
        $key      = self::getKey();
        $ivLength = openssl_cipher_iv_length(self::$cipher);
        $iv       = random_bytes($ivLength);   // CSPRNG — never use openssl_random_pseudo_bytes
        $tag      = '';

        $encrypted = openssl_encrypt(
            $text,
            self::$cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::$tagLength
        );

        if ($encrypted === false) {
            throw new Exception('Encryption failed');
        }

        // Wire format: base64(iv | tag | ciphertext)
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Decrypt a payload produced by encrypt().
     * Throws if the payload is malformed or the authentication tag does not match.
     */
    public static function decrypt(string $encryptedText): string
    {
        $key      = self::getKey();
        $ivLength = openssl_cipher_iv_length(self::$cipher);
        $minLen   = $ivLength + self::$tagLength;

        // strict=true: fails if the string is not valid base64
        $raw = base64_decode($encryptedText, strict: true);

        if ($raw === false || strlen($raw) < $minLen) {
            throw new Exception('Invalid encrypted payload');
        }

        $iv        = substr($raw, 0, $ivLength);
        $tag       = substr($raw, $ivLength, self::$tagLength);
        $encrypted = substr($raw, $minLen);

        $decrypted = openssl_decrypt(
            $encrypted,
            self::$cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($decrypted === false) {
            throw new Exception('Decryption failed: authentication tag mismatch');
        }

        return $decrypted;
    }

    /** Hash a plain-text password using Argon2ID. */
    public static function hash(string $text): string
    {
        return password_hash($text, PASSWORD_ARGON2ID);
    }

    /** Verify a plain-text value against an Argon2ID hash. */
    public static function verify(string $text, string $hash): bool
    {
        return password_verify($text, $hash);
    }

    /** Generate a cryptographically secure random hex token. */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Derive a 256-bit key from the RANDOM_SEED constant using SHA-256.
     * Throws RuntimeException if the constant is not defined or is empty.
     */
    private static function getKey(): string
    {
        if (!defined('RANDOM_SEED') || trim((string) RANDOM_SEED) === '') {
            throw new RuntimeException(
                'RANDOM_SEED is not defined. Define this constant before using Crypt.'
            );
        }

        return hash('sha256', RANDOM_SEED, true);
    }
}
