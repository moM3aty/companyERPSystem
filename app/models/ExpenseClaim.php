<?php
// app/models/ExpenseClaim.php

class ExpenseClaim extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'expense_claims';
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
            'company_id'      => "INT DEFAULT 1",
            'claim_number'    => "VARCHAR(50) NOT NULL",
            'employee_id'     => "INT NOT NULL",
            'department_id'   => "INT NULL",
            'claim_date'      => "DATE NOT NULL",
            'expense_type'    => "VARCHAR(100) NOT NULL",
            'amount'          => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'vat_amount'      => "DECIMAL(15,2) DEFAULT 0.00",
            'currency'        => "VARCHAR(10) DEFAULT 'SAR'",
            'project_id'      => "INT NULL",
            'cost_center'     => "VARCHAR(100) NULL",
            'business_purpose'=> "TEXT NOT NULL",
            'receipt_attachment'=> "VARCHAR(255) NULL",
            'manager_approval'=> "VARCHAR(50) DEFAULT 'Pending'", // Pending, Approved, Rejected
            'finance_approval'=> "VARCHAR(50) DEFAULT 'Pending'",
            'payment_status'  => "VARCHAR(50) DEFAULT 'Unpaid'",
            'treasury_id'     => "INT NULL", // To record from where it was paid
            'created_at'      => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllClaims() {
        $sql = "SELECT ec.*, e.full_name as employee_name, p.name as project_name 
                FROM {$this->table} ec 
                LEFT JOIN employees e ON ec.employee_id = e.id 
                LEFT JOIN projects p ON ec.project_id = p.id 
                WHERE ec.company_id = :cid ORDER BY ec.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getClaimById($id) {
        $sql = "SELECT ec.*, e.full_name as employee_name, p.name as project_name, t.name as treasury_name 
                FROM {$this->table} ec 
                LEFT JOIN employees e ON ec.employee_id = e.id 
                LEFT JOIN projects p ON ec.project_id = p.id 
                LEFT JOIN treasuries t ON ec.treasury_id = t.id
                WHERE ec.id = :id AND ec.company_id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createClaim($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, claim_number, employee_id, claim_date, expense_type, amount, vat_amount, currency, project_id, cost_center, business_purpose, receipt_attachment) 
                VALUES (:cid, :cnum, :emp, :cdate, :type, :amt, :vat, :curr, :proj, :cc, :purpose, :attach)";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':cnum', $data['claim_number']);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':cdate', $data['claim_date']);
        $this->db->bind(':type', $data['expense_type']);
        $this->db->bind(':amt', $data['amount']);
        $this->db->bind(':vat', $data['vat_amount']);
        $this->db->bind(':curr', $data['currency']);
        $this->db->bind(':proj', !empty($data['project_id']) ? $data['project_id'] : null);
        $this->db->bind(':cc', $data['cost_center']);
        $this->db->bind(':purpose', $data['business_purpose']);
        $this->db->bind(':attach', $data['receipt_attachment']);
        
        return $this->db->execute();
    }

    public function approveClaim($id, $level, $status, $treasuryId = null) {
        $this->db->beginTransaction();
        try {
            if ($level == 'manager') {
                $this->db->query("UPDATE {$this->table} SET manager_approval = :st WHERE id = :id AND company_id = :cid");
                $this->db->bind(':st', $status);
                $this->db->bind(':id', $id);
                $this->db->bind(':cid', Session::get('company_id') ?: 1);
                $this->db->execute();
            } elseif ($level == 'finance' && $status == 'Approved' && $treasuryId) {
                // Finance approval means payment is made
                $claim = $this->getClaimById($id);
                
                $this->db->query("UPDATE {$this->table} SET finance_approval = 'Approved', payment_status = 'Paid', treasury_id = :tid WHERE id = :id AND company_id = :cid");
                $this->db->bind(':tid', $treasuryId);
                $this->db->bind(':id', $id);
                $this->db->bind(':cid', Session::get('company_id') ?: 1);
                $this->db->execute();

                // Deduct from treasury
                $this->db->query("UPDATE treasuries SET current_balance = current_balance - :amt WHERE id = :tid");
                $this->db->bind(':amt', $claim->amount + $claim->vat_amount);
                $this->db->bind(':tid', $treasuryId);
                $this->db->execute();

                // Create Journal Entry
                $this->createExpenseJournal($claim, $treasuryId);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function createExpenseJournal($claim, $treasuryId) {
        $this->db->query("SELECT chart_account_id FROM treasuries WHERE id = :tid");
        $this->db->bind(':tid', $treasuryId);
        $treasuryAcc = $this->db->single();
        $cashAccId = $treasuryAcc ? $treasuryAcc->chart_account_id : null;

        $this->db->query("SELECT id FROM accounting_accounts WHERE account_type = 'Expense' LIMIT 1");
        $exp = $this->db->single();
        $expAccId = $exp ? $exp->id : null;

        if ($cashAccId && $expAccId) {
            require_once '../app/models/JournalEntry.php';
            $jeModel = new JournalEntry();
            
            $total = $claim->amount + $claim->vat_amount;
            $jeData = [
                'journal_number' => 'JV-EXC-' . time(),
                'date' => date('Y-m-d'),
                'description' => "مطالبة مصروفات: {$claim->employee_name} ({$claim->business_purpose})",
                'total_amount' => $total
            ];

            $lines = [
                ['account_id' => $expAccId, 'description' => $claim->business_purpose, 'debit' => $claim->amount, 'credit' => 0],
                ['account_id' => $cashAccId, 'description' => "صرف عهدة للموظف", 'debit' => 0, 'credit' => $total]
            ];
            
            if ($claim->vat_amount > 0) {
                $this->db->query("SELECT id FROM accounting_accounts WHERE account_name LIKE '%VAT%' OR account_name LIKE '%ضريبة%' LIMIT 1");
                $taxAcc = $this->db->single();
                if ($taxAcc) {
                    $lines[] = ['account_id' => $taxAcc->id, 'description' => "ضريبة مصروفات", 'debit' => $claim->vat_amount, 'credit' => 0];
                } else {
                    $lines[0]['debit'] += $claim->vat_amount;
                }
            }
            
            $jeModel->createEntry($jeData, $lines);
        }
    }
}