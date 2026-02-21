<?php

declare(strict_types=1);

namespace Sopaipilla\Database;

use PDO;
use PDOException;
use ReflectionClass;

abstract class Model
{
    // ── Model configuration ───────────────────────────────────────
    protected static string $table      = '';   // auto-derived if empty
    protected static string $primaryKey = 'id';
    protected static string $connection = '';   // empty = uses default connection
    protected static array  $fillable   = [];   // empty = all fields allowed
    protected static array  $schema     = [];   // SQL columns for auto-migration

    // ── Connection pool (one PDO per connection name) ──────────────────
    private static array $pool = [];

    // ────────────────────────────────────────────────────────────
    // PDO connection
    // ─────────────────────────────────────────────────────────────
    protected static function pdo(): PDO
    {
        $configPath = dirname(__DIR__, 2) . '/App/database.php';
        $config     = require $configPath;

        $name = static::$connection !== '' ? static::$connection : $config['default'];

        if (isset(self::$pool[$name])) {
            return self::$pool[$name];
        }

        if (!isset($config['connections'][$name])) {
            throw new \RuntimeException("Connection '{$name}' not defined in database.php");
        }

        $conn = $config['connections'][$name];

        $dsn = match ($conn['driver']) {
            'sqlite' => 'sqlite:' . $conn['database'],
            'mysql'  => sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $conn['host'],
                $conn['database'],
                $conn['charset'] ?? 'utf8mb4'
            ),
            default => throw new \RuntimeException('Unsupported driver: ' . $conn['driver']),
        };

        self::$pool[$name] = new PDO(
            $dsn,
            $conn['username'] ?? null,
            $conn['password'] ?? null,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );

        return self::$pool[$name];
    }

    // ── Internal helpers ─────────────────────────────────────────────────────

    /** Table name: uses $table or derives it from the class name (UsersModel → users). */
    protected static function tableName(): string
    {
        if (static::$table !== '') {
            return static::$table;
        }

        $short = (new ReflectionClass(static::class))->getShortName();
        return strtolower((string) preg_replace('/Model$/', '', $short));
    }

    /** Filters $data to only the fields allowed by $fillable. */
    protected static function filterFillable(array $data): array
    {
        if (empty(static::$fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip(static::$fillable));
    }

    /**
     * Creates the table based on the $schema array if defined.
     * Useful for SQLite :memory: and test environments.
     * Example: ['id INTEGER PRIMARY KEY AUTOINCREMENT', 'name TEXT NOT NULL']
     */
    public static function migrate(): void
    {
        if (empty(static::$schema)) {
            return;
        }

        $columns = implode(', ', static::$schema);
        static::pdo()->exec(
            'CREATE TABLE IF NOT EXISTS ' . static::tableName() . " ({$columns})"
        );
    }

    // ─────────────────────────────────────────────────────────────
    // Public API ─ CRUD
    // ────────────────────────────────────────────────────────────

    /** Returns all records. */
    public static function all(): array
    {
        return static::pdo()
            ->query('SELECT * FROM ' . static::tableName())
            ->fetchAll();
    }

    /** Finds a record by primary key; returns null if not found. */
    public static function find(int|string $id): ?array
    {
        $pk   = static::$primaryKey;
        $stmt = static::pdo()->prepare(
            'SELECT * FROM ' . static::tableName() . " WHERE {$pk} = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    /** Inserts a new record and returns the persisted array. */
    public static function create(array $data): array
    {
        $data         = static::filterFillable($data);
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $pdo          = static::pdo();

        $pdo->prepare(
            'INSERT INTO ' . static::tableName() . " ({$columns}) VALUES ({$placeholders})"
        )->execute(array_values($data));

        return static::find((int) $pdo->lastInsertId()) ?? $data;
    }

    /** Updates a record by PK; returns the updated array or null if not found. */
    public static function update(int|string $id, array $data): ?array
    {
        $data = static::filterFillable($data);

        if (empty($data)) {
            return static::find($id);
        }

        $pk  = static::$primaryKey;
        $set = implode(', ', array_map(fn ($col) => "{$col} = ?", array_keys($data)));

        $stmt = static::pdo()->prepare(
            'UPDATE ' . static::tableName() . " SET {$set} WHERE {$pk} = ?"
        );
        $stmt->execute([...array_values($data), $id]);

        return $stmt->rowCount() > 0 ? static::find($id) : null;
    }

    /** Deletes a record by PK; returns true if deleted, false if not found. */
    public static function delete(int|string $id): bool
    {
        $pk   = static::$primaryKey;
        $stmt = static::pdo()->prepare(
            'DELETE FROM ' . static::tableName() . " WHERE {$pk} = ?"
        );
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Simple WHERE query.
     * Returns an array of rows, or a single row when $single is true.
     */
    public static function where(string $column, mixed $value, bool $single = false): array|null
    {
        $stmt = static::pdo()->prepare(
            'SELECT * FROM ' . static::tableName() . " WHERE {$column} = ?"
        );
        $stmt->execute([$value]);

        if ($single) {
            $row = $stmt->fetch();
            return $row !== false ? $row : null;
        }

        return $stmt->fetchAll();
    }
}
