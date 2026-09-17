<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function db(): PDO
    {
        return Database::getConnection();
    }

    public function all(string $orderBy = 'id ASC'): array
    {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int|string $id): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :val LIMIT 1");
        $stmt->execute(['val' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function where(string $whereClause, array $params = [], string $orderBy = '', ?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE {$whereClause}";
        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $whereClause = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}`";
        if (!empty($whereClause)) {
            $sql .= " WHERE {$whereClause}";
        }
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);

        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $this->table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($data);

        return (int)$this->db()->lastInsertId();
    }

    public function update(int|string $id, array $data): bool
    {
        $fields = [];
        foreach ($data as $column => $val) {
            $fields[] = "`{$column}` = :{$column}";
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `%s` = :primary_id',
            $this->table,
            implode(', ', $fields),
            $this->primaryKey
        );

        $data['primary_id'] = $id;
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int|string $id): bool
    {
        $stmt = $this->db()->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id");
        return $stmt->execute(['id' => $id]);
    }
}
