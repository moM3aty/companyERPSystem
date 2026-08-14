<?php
// app/views/expenses/edit.php
$expense = $data['expense'] ?? null;
$treasuries = $data['treasuries'] ?? [];
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل بيانات مصروف</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/expense/edit/<?php echo $expense->id; ?>" method="POST">
        <div class="card-body">
            
            <div class="alert alert-info mb-4" style="font-size: 13px;">
                <i class="fas fa-info-circle"></i> تعديل المبلغ أو الخزنة سيقوم بتسوية وتعديل الأرصدة البنكية تلقائياً.
            </div>

            <div class="form-grid" style="grid-template-columns: 1fr;">
                <div class="form-group mb-0">
                    <label class="form-label">المبلغ المصروف (ر.س) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-monospace fw-black text-danger text-center" value="<?php echo $expense->amount; ?>" style="font-size: 24px; direction:ltr;" required>
                </div>

                <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group mb-0 mt-3">
                        <label class="form-label">تاريخ الصرف <span class="required">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="<?php echo $expense->expense_date; ?>" required>
                    </div>
                    <div class="form-group mb-0 mt-3">
                        <label class="form-label">بند المصروفات (التصنيف) <span class="required">*</span></label>
                        <select name="category" class="form-control fw-bold" required>
                            <option value="رواتب وأجور" <?php echo $expense->category == 'رواتب وأجور' ? 'selected':''; ?>>رواتب وأجور</option>
                            <option value="إيجارات" <?php echo $expense->category == 'إيجارات' ? 'selected':''; ?>>إيجارات</option>
                            <option value="كهرباء ومياه" <?php echo $expense->category == 'كهرباء ومياه' ? 'selected':''; ?>>كهرباء ومياه</option>
                            <option value="صيانة ونظافة" <?php echo $expense->category == 'صيانة ونظافة' ? 'selected':''; ?>>صيانة ونظافة</option>
                            <option value="تسويق وإعلانات" <?php echo $expense->category == 'تسويق وإعلانات' ? 'selected':''; ?>>تسويق وإعلانات</option>
                            <option value="ضيافة وبوفيه" <?php echo $expense->category == 'ضيافة وبوفيه' ? 'selected':''; ?>>ضيافة وبوفيه</option>
                            <option value="أدوات مكتبية" <?php echo $expense->category == 'أدوات مكتبية' ? 'selected':''; ?>>أدوات مكتبية وقرطاسية</option>
                            <option value="رسوم حكومية" <?php echo $expense->category == 'رسوم حكومية' ? 'selected':''; ?>>رسوم حكومية وضرائب</option>
                            <option value="نثريات أخرى" <?php echo $expense->category == 'نثريات أخرى' ? 'selected':''; ?>>نثريات أخرى</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-0 mt-3 border rounded p-3 bg-light">
                    <label class="form-label">تم السحب من (الخزنة / البنك)</label>
                    <select name="treasury_id" class="form-control fw-bold text-primary">
                        <option value="">-- دفع من عهدة شخصية (لن يخصم من الخزائن) --</option>
                        <?php foreach($treasuries as $t): ?>
                            <option value="<?php echo $t->id; ?>" <?php echo $expense->treasury_id == $t->id ? 'selected':''; ?>><?php echo htmlspecialchars($t->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-0 mt-3">
                    <label class="form-label">رقم المرجع (فاتورة / إيصال)</label>
                    <input type="text" name="reference" class="form-control font-monospace" value="<?php echo htmlspecialchars($expense->reference ?? ''); ?>">
                </div>

                <div class="form-group mb-0 mt-3">
                    <label class="form-label">البيان والملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($expense->notes ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات وتسوية الرصيد</button>
            <a href="<?php echo URLROOT; ?>/expense/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>