<?php
namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected ?int $tenantId = null;

    public function __construct()
    {
        $this->db = Database::getInstance();
        if (isset($_SESSION['company_id'])) {
            $this->tenantId = (int) $_SESSION['company_id'];
        }
    }

    public function setTenantId(?int $id): void
    {
        $this->tenantId = $id;
    }

    protected function tenantColumn(): string
    {
        return 'company_id';
    }

    protected function tenantWhere(): string
    {
        if ($this->tenantId === null) {
            return '1=1';
        }
        return $this->tenantColumn() . ' = ' . (int) $this->tenantId;
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        if ($this->tenantId !== null && $this->table !== 'companies' && $this->table !== 'users') {
            $sql .= " AND " . $this->tenantColumn() . " = ?";
        }
        $stmt = $this->db->prepare($sql);
        $params = [$id];
        if ($this->tenantId !== null && $this->table !== 'companies' && $this->table !== 'users') {
            $params[] = $this->tenantId;
        }
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(string $orderBy = 'id', string $dir = 'ASC'): array
    {
        $orderBy = preg_replace('/[^a-z0-9_]/i', '', $orderBy);
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM {$this->table} WHERE {$this->tenantWhere()} ORDER BY {$orderBy} {$dir}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $tenantCol = $this->tenantColumn();
        if ($this->tenantId !== null && !isset($data[$tenantCol])) {
            $data[$tenantCol] = $this->tenantId;
        }
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach (array_keys($data) as $col) {
            $sets[] = "`$col` = ?";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->primaryKey} = ?";
        $params = array_values($data);
        $params[] = $id;
        if ($this->tenantId !== null && $this->table !== 'companies' && $this->table !== 'users') {
            $sql .= " AND " . $this->tenantColumn() . " = ?";
            $params[] = $this->tenantId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        if ($this->tenantId !== null && $this->table !== 'companies' && $this->table !== 'users') {
            $sql .= " AND " . $this->tenantColumn() . " = ?";
        }
        $stmt = $this->db->prepare($sql);
        $params = [$id];
        if ($this->tenantId !== null && $this->table !== 'companies' && $this->table !== 'users') {
            $params[] = $this->tenantId;
        }
        return $stmt->execute($params);
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row ?: null;
    }
}
