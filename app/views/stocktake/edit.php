<?php
// app/views/stocktake/edit.php
$stocktake = $data['stocktake'] ?? null;
?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-pen text-accent"></i> تعديل بيانات الجرد: <?php echo htmlspecialchars($stocktake->reference ?? ''); ?></h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/stocktake/edit/<?php echo $stocktake->id; ?>" method="POST">
        <div class="card-body">
            
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle"></i> ملاحظة: تغيير "حالة الجرد" إلى (معتمد) قد يؤثر على أرصدة المخزون الفعلية، يرجى توخي الحذر.
            </div>

            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الرقم المرجعي للجرد (لا يمكن تعديله)</label>
                    <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($stocktake->reference ?? ''); ?>" readonly disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ عملية الجرد <span class="required">*</span></label>
                    <input type="date" name="stocktake_date" class="form-control" value="<?php echo $stocktake->stocktake_date ?? date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الجرد <span class="required">*</span></label>
                    <select name="status" class="form-control fw-bold">
                        <option value="draft" <?php echo ($stocktake->status == 'draft') ? 'selected' : ''; ?>>مسودة (Draft)</option>
                        <option value="in_progress" <?php echo ($stocktake->status == 'in_progress') ? 'selected' : ''; ?>>قيد التنفيذ (In Progress)</option>
                        <option value="completed" <?php echo ($stocktake->status == 'completed') ? 'selected' : ''; ?>>معتمد / مكتمل (Completed)</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات عن الجرد</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="أدخل أية ملاحظات توضيحية بخصوص هذه العملية..."><?php echo htmlspecialchars($stocktake->notes ?? ''); ?></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/stocktake/index" class="btn btn-secondary">إلغاء الرجوع</a>
        </div>
    </form>
</div>