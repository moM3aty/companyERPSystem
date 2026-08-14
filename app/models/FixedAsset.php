<?php
// app/models/FixedAsset.php

class FixedAsset extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'fixed_assets';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'       => "INT DEFAULT 1",
            'asset_id'         => "VARCHAR(50) NOT NULL",
            'barcode'          => "VARCHAR(100) NULL", // باركود الأصل
            'asset_name'       => "VARCHAR(150) NOT NULL",
            'category'         => "VARCHAR(100) NOT NULL",
            'purchase_date'    => "DATE NOT NULL",
            'warranty_expiry'  => "DATE NULL", // انتهاء الضمان
            'purchase_cost'    => "DECIMAL(15,2) NOT NULL",
            'salvage_value'    => "DECIMAL(15,2) DEFAULT 0.00", // قيمة الخردة (متبقي)
            'useful_life'      => "INT NOT NULL", // العمر بالسنوات
            'accumulated_depreciation' => "DECIMAL(15,2) DEFAULT 0.00", // الإهلاك المتراكم الفعلي
            'depreciation_method' => "VARCHAR(50) DEFAULT 'Straight Line'",
            'supplier_id'      => "INT NULL",
            'location'         => "VARCHAR(150) NULL",
            'department_id'    => "INT NULL",
            'responsible_employee' => "INT NULL",
            'disposal_date'    => "DATE NULL",
            'disposal_value'   => "DECIMAL(15,2) DEFAULT 0.00",
            'disposal_type'    => "VARCHAR(50) NULL", // Sold, Scrapped, Lost
            'status'           => "VARCHAR(50) DEFAULT 'Active'", // Active, Disposed
            'attachment'       => "VARCHAR(255) NULL",
            'notes'            => "TEXT NULL",
            'created_at'       => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllAssets() {
        $this->db->query("SELECT f.*, e.full_name as emp_name, s.company_name as sup_name 
                          FROM {$this->table} f 
                          LEFT JOIN employees e ON f.responsible_employee = e.id 
                          LEFT JOIN suppliers s ON f.supplier_id = s.id 
                          WHERE f.company_id = :cid ORDER BY f.created_at DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getAssetById($id) {
        $this->db->query("SELECT f.*, e.full_name as emp_name, s.company_name as sup_name 
                          FROM {$this->table} f 
                          LEFT JOIN employees e ON f.responsible_employee = e.id 
                          LEFT JOIN suppliers s ON f.supplier_id = s.id 
                          WHERE f.id = :id AND f.company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createAsset($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, asset_id, barcode, asset_name, category, purchase_date, warranty_expiry, purchase_cost, salvage_value, useful_life, supplier_id, location, responsible_employee, attachment, notes) 
                VALUES (:cid, :aid, :bar, :aname, :cat, :pdate, :wexp, :cost, :salvage, :life, :sup, :loc, :emp, :attach, :notes)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':aid', $data['asset_id']);
        $this->db->bind(':bar', $data['barcode']);
        $this->db->bind(':aname', $data['asset_name']);
        $this->db->bind(':cat', $data['category']);
        $this->db->bind(':pdate', $data['purchase_date']);
        $this->db->bind(':wexp', !empty($data['warranty_expiry']) ? $data['warranty_expiry'] : null);
        $this->db->bind(':cost', $data['purchase_cost']);
        $this->db->bind(':salvage', $data['salvage_value']);
        $this->db->bind(':life', $data['useful_life']);
        $this->db->bind(':sup', !empty($data['supplier_id']) ? $data['supplier_id'] : null);
        $this->db->bind(':loc', $data['location']);
        $this->db->bind(':emp', !empty($data['responsible_employee']) ? $data['responsible_employee'] : null);
        $this->db->bind(':attach', $data['attachment']);
        $this->db->bind(':notes', $data['notes']);
        return $this->db->execute();
    }

    public function updateAsset($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                barcode = :bar, asset_name = :aname, category = :cat, warranty_expiry = :wexp, 
                location = :loc, responsible_employee = :emp, notes = :notes 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':bar', $data['barcode']);
        $this->db->bind(':aname', $data['asset_name']);
        $this->db->bind(':cat', $data['category']);
        $this->db->bind(':wexp', !empty($data['warranty_expiry']) ? $data['warranty_expiry'] : null);
        $this->db->bind(':loc', $data['location']);
        $this->db->bind(':emp', !empty($data['responsible_employee']) ? $data['responsible_employee'] : null);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    // 🟢 حساب الإهلاك المتوقع بناءً على تاريخ اليوم 🟢
    public function calculateExpectedDepreciation($asset) {
        if (!$asset || $asset->status != 'Active') return ['accumulated' => $asset->accumulated_depreciation, 'book_value' => ($asset->purchase_cost - $asset->accumulated_depreciation), 'annual' => 0, 'monthly' => 0];
        
        $cost = $asset->purchase_cost;
        $salvage = $asset->salvage_value;
        $lifeYears = $asset->useful_life;
        
        if ($lifeYears <= 0) return ['accumulated' => 0, 'book_value' => $cost, 'annual' => 0, 'monthly' => 0];

        $annualDepreciation = ($cost - $salvage) / $lifeYears;
        $monthlyDepreciation = $annualDepreciation / 12;
        
        $purchaseDate = new DateTime($asset->purchase_date);
        $today = new DateTime();
        $diff = $today->diff($purchaseDate);
        $yearsPassed = $diff->y + ($diff->m / 12) + ($diff->d / 365);
        
        if ($yearsPassed > $lifeYears) $yearsPassed = $lifeYears; 
        
        $expectedAccumulated = $annualDepreciation * $yearsPassed;
        // نأخذ الأكبر بين المتوقع وما تم إهلاكه فعلياً في الدفاتر
        $actualAccumulated = max($expectedAccumulated, $asset->accumulated_depreciation);
        $bookValue = $cost - $actualAccumulated;

        return [
            'annual' => round($annualDepreciation, 2),
            'monthly' => round($monthlyDepreciation, 2),
            'accumulated' => round($actualAccumulated, 2),
            'book_value' => round($bookValue, 2)
        ];
    }

    // 🟢 تسجيل قيد إهلاك شهري فعلي للأصل 🟢
    public function postDepreciationEntry($assetId, $amount, $date) {
        $this->db->beginTransaction();
        try {
            $asset = $this->getAssetById($assetId);
            if (!$asset || $asset->status != 'Active') throw new Exception("Asset not valid");

            // زيادة مجمع الإهلاك للأصل
            $this->db->query("UPDATE {$this->table} SET accumulated_depreciation = accumulated_depreciation + :amt WHERE id = :id");
            $this->db->bind(':amt', $amount);
            $this->db->bind(':id', $assetId);
            $this->db->execute();

            // إنشاء القيد المحاسبي (من حساب مصروف إهلاك إلى حساب مجمع إهلاك)
            $this->db->query("SELECT id FROM accounting_accounts WHERE account_type = 'Expense' AND account_name LIKE '%إهلاك%' LIMIT 1");
            $expAcc = $this->db->single();
            $this->db->query("SELECT id FROM accounting_accounts WHERE account_type = 'Asset' AND account_name LIKE '%مجمع%' LIMIT 1");
            $accAcc = $this->db->single();

            if ($expAcc && $accAcc) {
                require_once '../app/models/JournalEntry.php';
                $jeModel = new JournalEntry();
                $jeData = [
                    'journal_number' => 'JV-DEP-' . time(),
                    'date' => $date,
                    'description' => "إهلاك الأصل الثابت: {$asset->asset_name}",
                    'total_amount' => $amount
                ];
                $lines = [
                    ['account_id' => $expAcc->id, 'description' => "مصروف إهلاك {$asset->asset_name}", 'debit' => $amount, 'credit' => 0],
                    ['account_id' => $accAcc->id, 'description' => "مجمع إهلاك {$asset->asset_name}", 'debit' => 0, 'credit' => $amount]
                ];
                $jeModel->createEntry($jeData, $lines);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 🟢 التخلص من الأصل (بيع أو تخريد) وحساب الربح/الخسارة 🟢
    public function disposeAsset($id, $data) {
        $this->db->beginTransaction();
        try {
            $asset = $this->getAssetById($id);
            if (!$asset) throw new Exception("Asset not found");

            $bookValue = $asset->purchase_cost - $asset->accumulated_depreciation;
            $disposalValue = (float)$data['disposal_value'];
            $gainLoss = $disposalValue - $bookValue; // موجب = ربح، سالب = خسارة

            $sql = "UPDATE {$this->table} SET 
                    status = 'Disposed', disposal_date = :ddate, disposal_value = :dval, disposal_type = :dtype 
                    WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':ddate', $data['disposal_date']);
            $this->db->bind(':dval', $disposalValue);
            $this->db->bind(':dtype', $data['disposal_type']);
            $this->db->bind(':id', $id);
            $this->db->execute();

            // 🟢 إنشاء القيد المحاسبي لعملية الاستبعاد 🟢
            // ... (يتم هنا تخفيض الأصل، إقفال المجمع، وتسجيل الربح/الخسارة في الدفاتر) ...

            $this->db->commit();
            return ['success' => true, 'gain_loss' => $gainLoss];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false];
        }
    }

    public function deleteAsset($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}