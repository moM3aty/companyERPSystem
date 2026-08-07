<?php
// app/views/expenses/create.php
$categories = $categories ?? ($data['categories'] ?? []);
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-danger text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-file-invoice-dollar"></i> توثيق مصروف جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/expense/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">تصنيف المصروف <span class="required">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- يرجى اختيار التصنيف المالي --</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo $cat->id; ?>"><?php echo htmlspecialchars($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">المبلغ المالي (ر.س) <span class="required">*</span></label>
                    <input type="number" name="amount" class="form-control font-monospace text-danger fw-bold" step="0.01" min="0.01" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ المصروف <span class="required">*</span></label>
                    <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">رقم المرجع / الفاتورة (اختياري)</label>
                    <input type="text" name="reference_no" class="form-control font-monospace" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">البيان والملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="تفاصيل المصروف..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> حفظ المصروف</button>
            <a href="<?php echo URLROOT; ?>/expense/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>