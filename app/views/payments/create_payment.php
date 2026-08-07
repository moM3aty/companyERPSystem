<?php
// المسار: app/views/payments/create_payment.php
$pos = $pos ?? [];
?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-danger text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-arrow-up"></i> سداد دفعة لمورد (سند صرف)</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/payment/createPayment" method="POST">
        <div class="card-body border-bottom bg-light">
            <div class="alert alert-danger">
                <i class="fas fa-info-circle"></i> سيؤدي هذا السند إلى تخفيض المديونية (الرصيد الدائن) المستحقة للمورد.
            </div>
            <div class="form-grid" style="grid-template-columns: 1fr;">
                <div class="form-group">
                    <label class="form-label">أمر الشراء المرتبط <span class="required">*</span></label>
                    <select name="po_id" class="form-control" required>
                        <option value="">-- اختر أمر الشراء --</option>
                        <?php foreach($pos as $po): ?>
                            <option value="<?php echo $po->id; ?>">أمر #<?php echo htmlspecialchars($po->po_number); ?> - <?php echo htmlspecialchars($po->supplier_name); ?> (المبلغ: <?php echo number_format($po->total_amount, 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">المبلغ المدفوع (ر.س) <span class="required">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control font-monospace fw-bold text-danger" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">طريقة السداد</label>
                    <select name="payment_method" class="form-control">
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="cash">نقدي (كاش)</option>
                        <option value="check">شيك مصدق</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات / رقم الحوالة</label>
                    <input type="text" name="notes" class="form-control" placeholder="رقم عملية التحويل أو الملاحظات...">
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> تأكيد الصرف</button>
            <a href="<?php echo URLROOT; ?>/payment/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>