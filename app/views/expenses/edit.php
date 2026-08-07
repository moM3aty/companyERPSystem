<?php
// app/views/expenses/edit.php
$expense = $expense ?? ($data['expense'] ?? null);
$categories = $categories ?? ($data['categories'] ?? []);
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل المصروف</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/expense/edit/<?php echo $expense->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">تصنيف المصروف <span class="required">*</span></label>
                    <select name="category_id" class="form-control" required>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo $cat->id; ?>" <?php echo $expense->category_id == $cat->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">المبلغ المالي (ر.س) <span class="required">*</span></label>
                    <input type="number" name="amount" class="form-control font-monospace text-danger fw-bold" step="0.01" min="0.01" value="<?php echo $expense->amount; ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ المصروف <span class="required">*</span></label>
                    <input type="date" name="expense_date" class="form-control" value="<?php echo $expense->expense_date; ?>" required>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">رقم المرجع / الفاتورة (اختياري)</label>
                    <input type="text" name="reference_no" class="form-control font-monospace" value="<?php echo htmlspecialchars($expense->reference_no); ?>" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">البيان والملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($expense->notes); ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/expense/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>