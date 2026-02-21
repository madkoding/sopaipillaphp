<?php

declare(strict_types=1);

namespace App\Users;

use Sopaipilla\Database\Model;

/**
 * ORM model for the `users` table.
 *
 * Extends the base Model with a custom changePassword() method.
 * The $fillable whitelist deliberately excludes password_hash to prevent
 * mass-assignment of sensitive fields.
 */
class UsersModel extends Model
{
    protected static string $table      = 'users';
    protected static string $connection = 'sqlite';

    /** Only these fields are writable via create() / update(). */
    protected static array $fillable = ['name', 'email'];

    /** Schema for auto-creating the table (useful with SQLite :memory:). */
    protected static array $schema = [
        'id            INTEGER PRIMARY KEY AUTOINCREMENT',
        'name          TEXT    NOT NULL',
        'email         TEXT    NOT NULL',
        'password_hash TEXT',
    ];

    /**
     * Updates the user's password hash.
     * Verification of the current password is the caller's responsibility.
     */
    public static function changePassword(int $id, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
        $stmt = static::pdo()->prepare(
            'UPDATE ' . static::tableName() . ' SET password_hash = ? WHERE id = ?'
        );
        $stmt->execute([$hash, $id]);
    }
}
