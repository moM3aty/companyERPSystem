<?php
// المسار: app/models/FixedAsset.php

class FixedAsset extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'fixed_assets';
    }

    /**
     * جلب جميع الأصول الثابتة
     */
    public function getAllAssets(): array {
        $sql = "SELECT f.*, u.name as added_by 
                FROM {$this->table} f 
                LEFT JOIN users u ON f.created_by = u.id 
                ORDER BY f.purchase_date DESC, f.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * جلب تفاصيل أصل معين
     */
    public function getAssetById(int $id): ?object {
        $sql = "SELECT f.*, u.name as added_by 
                FROM {$this->table} f 
                LEFT JOIN users u ON f.created_by = u.id 
                WHERE f.id = :id LIMIT 1";
                
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * إنشاء أصل ثابت جديد
     */
    public function createAsset(array $data): bool {
        // توليد رقم الأصل (Asset Tag) تلقائياً إذا لم يتم توفيره
        $assetTag = !empty($data['asset_tag']) ? $data['asset_tag'] : 'AST-' . date('Ym') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO {$this->table} 
                (asset_tag, name, category, purchase_date, purchase_cost, salvage_value, useful_life_years, location, status, notes, created_by, created_at) 
                VALUES 
                (:asset_tag, :name, :category, :purchase_date, :purchase_cost, :salvage_value, :useful_life_years, :location, :status, :notes, :created_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':asset_tag', $assetTag);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':purchase_date', $data['purchase_date']);
        $this->db->bind(':purchase_cost', $data['purchase_cost']);
        $this->db->bind(':salvage_value', $data['salvage_value']);
        $this->db->bind(':useful_life_years', $data['useful_life_years'], PDO::PARAM_INT);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}