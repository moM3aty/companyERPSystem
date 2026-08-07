<?php
// app/models/FixedAsset.php

class FixedAsset extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'fixed_assets';
    }

    public function getAllAssets(): array {
        $sql = "SELECT f.*, u.name as added_by 
                FROM {$this->table} f 
                LEFT JOIN users u ON f.created_by = u.id 
                ORDER BY f.purchase_date DESC, f.created_at DESC";
        $this->db->query($sql);
        $assets = $this->db->resultSet();
        
        // حساب الإهلاك الآني لكل أصل (Straight-line Depreciation)
        $currentDate = new DateTime();
        foreach ($assets as &$asset) {
            $purchaseDate = new DateTime($asset->purchase_date);
            $interval = $purchaseDate->diff($currentDate);
            $yearsElapsed = $interval->y + ($interval->m / 12) + ($interval->d / 365.25);
            
            if ($yearsElapsed < 0) $yearsElapsed = 0;
            if ($yearsElapsed > $asset->useful_life_years) $yearsElapsed = $asset->useful_life_years;
            
            $annualDepreciation = ($asset->purchase_cost - $asset->salvage_value) / $asset->useful_life_years;
            $accumulatedDepreciation = $annualDepreciation * $yearsElapsed;
            
            // إضافة الخصائص المحسوبة للكائن
            $asset->book_value = max(0, $asset->purchase_cost - $accumulatedDepreciation);
            $asset->accumulated_depreciation = $accumulatedDepreciation;
        }
        
        return $assets;
    }

    public function getAssetById(int $id): ?object {
        $sql = "SELECT f.* FROM {$this->table} f WHERE f.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createAsset(array $data): bool {
        try {
            $this->db->beginTransaction();
            
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
            $this->db->execute();

            $assetId = $this->db->lastInsertId();

            // 🟢 الربط المحاسبي الآلي: إثبات شراء الأصل الثابت (الدفع النقدي افتراضياً للتبسيط)
            $accountingModel = new Accounting();
            $dbCoa = $this->db;
            
            // جلب حساب الأصول الثابتة
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' AND (name LIKE '%أصول%' OR name LIKE '%معدات%') LIMIT 1");
            $assetAcc = $dbCoa->single();
            
            // جلب حساب الصندوق/البنك
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' AND name LIKE '%صندوق%' LIMIT 1");
            $cashAcc = $dbCoa->single();

            if ($assetAcc && $cashAcc) {
                $lines = [
                    ['account_id' => $assetAcc->id, 'debit' => $data['purchase_cost'], 'credit' => 0, 'description' => "شراء أصل ثابت: {$data['name']} ({$assetTag})"],
                    ['account_id' => $cashAcc->id, 'debit' => 0, 'credit' => $data['purchase_cost'], 'description' => "سداد قيمة الأصل الثابت"]
                ];
                $accountingModel->createJournalEntry(
                    $data['purchase_date'], 
                    "إثبات شراء أصل ثابت: {$data['name']}", 
                    'fixed_asset', 
                    $assetId, 
                    $data['created_by'], 
                    $lines
                );
            }

            ActivityLog::logAction('CREATE', 'FixedAssets', $assetId, "تم تسجيل أصل جديد بالمؤسسة: {$data['name']} بقيمة {$data['purchase_cost']}");
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateAsset(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET asset_tag = :asset_tag, name = :name, category = :category, purchase_date = :purchase_date, 
                    purchase_cost = :purchase_cost, salvage_value = :salvage_value, useful_life_years = :useful_life_years, 
                    location = :location, status = :status, notes = :notes 
                WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':asset_tag', $data['asset_tag']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':category', $data['category']);
        $this->db->bind(':purchase_date', $data['purchase_date']);
        $this->db->bind(':purchase_cost', $data['purchase_cost']);
        $this->db->bind(':salvage_value', $data['salvage_value']);
        $this->db->bind(':useful_life_years', $data['useful_life_years'], PDO::PARAM_INT);
        $this->db->bind(':location', $data['location']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function deleteAsset(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}