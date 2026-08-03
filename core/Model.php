<?php

/**
 * Model
 * Lightweight base class giving every model reusable query helpers
 * so child models stay small and declarative (OOP, no duplication).
 */
abstract class Model
{
    protected static string $table = '';
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    protected static function conn(): PDO
    {
        return Database::connection();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::conn()->prepare('SELECT * FROM ' . static::$table . ' WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBy(string $column, $value): ?array
    {
        $stmt = self::conn()->prepare('SELECT * FROM ' . static::$table . " WHERE {$column} = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        $stmt = self::conn()->query('SELECT * FROM ' . static::$table . ' ORDER BY ' . $orderBy);
        return $stmt->fetchAll();
    }

    public static function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);

        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($data);

        return (int) self::conn()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $sets = array_map(fn($c) => "{$c} = :{$c}", array_keys($data));
        $sql = 'UPDATE ' . static::$table . ' SET ' . implode(',', $sets) . ' WHERE id = :id';
        $data['id'] = $id;
        $stmt = self::conn()->prepare($sql);
        return $stmt->execute($data);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::conn()->prepare('DELETE FROM ' . static::$table . ' WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function count(string $where = '1', array $params = []): int
    {
        $stmt = self::conn()->prepare('SELECT COUNT(*) FROM ' . static::$table . ' WHERE ' . $where);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
