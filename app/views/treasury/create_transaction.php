<?php
// app/views/treasury/create_transaction.php
$treasuries = $data['treasuries'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice text-primary"></i> توثيق حركة مالية (سند قبض / صرف)</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/treasury/createTransaction" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الخزنة / الحساب البنكي <span class="required">*</span></label>
                    <select name="treasury_id" class="form-control" required>
                        <option value="">-- اختر الخزنة المستهدفة --</option>
                        <?php foreach($treasuries as $t): ?>
                            <option value="<?php echo $t->id; ?>"><?php echo htmlspecialchars($t->name); ?> (الرصيد: <?php echo number_format($t->current_balance, 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الحركة (السند) <span class="required">*</span></label>
                    <select name="transaction_type" class="form-control" required>
                        <option value="receipt">سند قبض (إيداع أموال)</option>
                        <option value="payment">سند صرف (خروج أموال)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">المبلغ (ر.س) <span class="required">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control font-monospace fw-bold text-primary" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الحركة <span class="required">*</span></label>
                    <input type="date" name="transaction_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الرقم المرجعي (اختياري)</label>
                    <input type="text" name="reference" class="form-control" placeholder="مثال: شيك #553">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">البيان (وصف الحركة) <span class="required">*</span></label>
                    <textarea name="description" class="form-control" rows="3" required placeholder="اكتب سبب الاستلام أو الصرف..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> تأكيد وحفظ السند</button>
            <a href="<?php echo URLROOT; ?>/treasury/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>