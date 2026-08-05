<?php
// core/Model.php

/**
 * النموذج الأساسي - كل النماذج ترث منه
 * يوفر طرق مساعدة شائعة للتعامل مع قاعدة البيانات
 */
abstract class Model {
    
    /** @var Database مثيل قاعدة البيانات */
    protected Database $db;
    
    /** @var string اسم الجدول */
    protected string $table = '';
    
    /** @var string المفتاح الأساسي */
    protected string $primaryKey = 'id';
    
    /**
     * تهيئة الاتصال بقاعدة البيانات
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * جلب كل السجلات
     */
    public function getAll(string $orderBy = 'id', string $orderDir = 'DESC'): array {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$orderDir}");
        return $this->db->resultSet();
    }
    
    /**
     * جلب سجل بالمعرف
     */
    public function findById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    
    /**
     * جلب سجل بحقل معين
     */
    public function findBy(string $field, $value, string $orderBy = 'id', string $orderDir = 'DESC'): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE {$field} = :value ORDER BY {$orderBy} {$orderDir} LIMIT 1");
        $this->db->bind(':value', $value);
        return $this->db->single();
    }
    
    /**
     * جلب سجلات بحقل معين
     */
    public function where(string $field, $value, string $orderBy = 'id', string $orderDir = 'DESC'): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE {$field} = :value ORDER BY {$orderBy} {$orderDir}");
        $this->db->bind(':value', $value);
        return $this->db->resultSet();
    }
    
    /**
     * البحث بالنص في أعمدة محددة
     */
    public function search(string $query, array $columns = []): array {
        if (empty($columns)) {
            $columns = [$this->primaryKey];
        }
        
        $conditions = [];
        foreach ($columns as $col) {
            $conditions[] = "{$col} LIKE :query";
        }
        
        $whereClause = implode(' OR ', $conditions);
        
        $this->db->query("SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY {$this->primaryKey} DESC");
        $this->db->bind(':query', '%' . $query . '%');
        return $this->db->resultSet();
    }
    
    /**
     * عد السجلات
     */
    public function count(): int {
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        $result = $this->db->single();
        return $result ? (int) $result->total : 0;
    }
    
    /**
     * مجموع عمود
     */
    public function sum(string $column, ?string $where = null): float {
        $sql = "SELECT COALESCE(SUM({$column}), 0) as total FROM {$this->table}";
        
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (float) $result->total : 0.0;
    }
    
    /**
     * إدراج سجل جديد
     */
    public function create(array $data): int {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $this->db->query("INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})");
        
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        $this->db->execute();
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * تحديث سجل
     */
    public function update(int $id, array $data): bool {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "{$key} = :{$key}";
        }
        
        $setString = implode(', ', $setClause);
        
        $this->db->query("UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = :id");
        
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
    
    /**
     * حذف سجل
     */
    public function delete(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    /**
     * حذف سجلات بحقل
     */
    public function deleteWhere(string $field, $value): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE {$field} = :value");
        $this->db->bind(':value', $value);
        return $this->db->execute();
    }
    
    /**
     * التحقق من وجود سجل
     */
    public function exists(int $id): bool {
        $this->db->query("SELECT 1 FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
    
    /**
     * تنفيذ استعلام مخصص
     */
    public function rawQuery(string $sql, array $params = []): array {
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }
}