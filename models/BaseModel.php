<?php
abstract class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // CRUD Operations
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $result = $this->db->execute($sql, array_values($data));
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findAll($conditions = [], $orderBy = 'id', $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        // Validate orderBy to prevent SQL injection
        $allowedOrderBy = preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*( (ASC|DESC))?$/i', $orderBy);
        if (!$allowedOrderBy) {
            $orderBy = 'id';
        }
        $sql .= " ORDER BY {$orderBy}";
        
        if ($limit && is_numeric($limit) && $limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $fields[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->db->execute($sql, $params) > 0;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
    
    public function exists($field, $value, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$field} = ?";
        $params = [$value];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] > 0;
    }
    
    // Soft delete (if table has deleted_at column)
    public function softDelete($id) {
        if ($this->hasColumn('deleted_at')) {
            return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        }
        
        return $this->delete($id);
    }
    
    // Restore soft deleted record
    public function restore($id) {
        if ($this->hasColumn('deleted_at')) {
            return $this->update($id, ['deleted_at' => null]);
        }
        
        return false;
    }
    
    // Count records
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'];
    }
    
    // Paginate results
    public function paginate($page = 1, $perPage = 10, $conditions = [], $orderBy = 'id DESC') {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $totalRecords = $this->count($conditions);
        $totalPages = ceil($totalRecords / $perPage);
        
        // Get records for current page
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        // Validate orderBy to prevent SQL injection
        $allowedOrderBy = preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*( (ASC|DESC))?$/i', $orderBy);
        if (!$allowedOrderBy) {
            $orderBy = 'id DESC';
        }
        
        // Sanitize pagination parameters
        $perPage = (int)$perPage;
        $offset = (int)$offset;
        
        $sql .= " ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        
        $records = $this->db->fetchAll($sql, $params);
        
        return [
            'data' => $records,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'has_next' => $page < $totalPages,
            'has_previous' => $page > 1
        ];
    }
    
    // Get distinct values for a column
    public function getDistinct($column, $conditions = []) {
        // Validate column name to prevent SQL injection
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            throw new InvalidArgumentException('Invalid column name');
        }
        
        $sql = "SELECT DISTINCT {$column} FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $sql .= " ORDER BY {$column}";
        
        $results = $this->db->fetchAll($sql, $params);
        return array_column($results, $column);
    }
    
    // Check if column exists in table
    private function hasColumn($columnName) {
        $sql = "SHOW COLUMNS FROM {$this->table} LIKE ?";
        $result = $this->db->fetchOne($sql, [$columnName]);
        return !empty($result);
    }
    
    // Get random records
    public function random($limit = 1, $conditions = []) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        // Sanitize limit parameter
        $limit = (int)$limit;
        if ($limit <= 0) $limit = 1;
        
        $sql .= " ORDER BY RAND() LIMIT {$limit}";
        
        if ($limit == 1) {
            return $this->db->fetchOne($sql, $params);
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Bulk insert
    public function bulkInsert($dataArray) {
        if (empty($dataArray)) {
            return false;
        }
        
        $firstRow = reset($dataArray);
        $fields = array_keys($firstRow);
        $placeholders = '(' . implode(', ', array_fill(0, count($fields), '?')) . ')';
        
        $values = [];
        $params = [];
        
        foreach ($dataArray as $data) {
            $values[] = $placeholders;
            $params = array_merge($params, array_values($data));
        }
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES " . implode(', ', $values);
        
        return $this->db->execute($sql, $params) > 0;
    }
    
    // Execute custom query
    protected function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }
    
    // Get table name
    public function getTable() {
        return $this->table;
    }
    
    // Transaction helpers
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    public function commit() {
        return $this->db->commit();
    }
    
    public function rollback() {
        return $this->db->rollback();
    }
    
    // Validation helpers
    protected function validateRequired($data, $fields) {
        $errors = [];
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "El campo {$field} es requerido";
            }
        }
        
        return $errors;
    }
    
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    protected function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    protected function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    // Get last error
    public function getLastError() {
        return $this->db->getConnection()->errorInfo();
    }
}
?>