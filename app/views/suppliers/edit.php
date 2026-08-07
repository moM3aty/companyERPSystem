<?php
// المسار: app/views/suppliers/edit.php
$supplier = $supplier ?? ($data['supplier'] ?? null);?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen" style="color: var(--accent);"></i> تعديل بيانات المورد: <?php echo htmlspecialchars($supplier->name); ?></h3>
    </div>
    
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/supplier/edit/<?php echo $supplier->id; ?>" method="POST">
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">اسم المورد أو الشركة <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($supplier->name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الشخص المسؤول (Contact Person)</label>
                    <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($supplier->contact_person); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">نوع المورد <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="company" <?php echo $supplier->type === 'company' ? 'selected' : ''; ?>>شركة (Company)</option>
                        <option value="individual" <?php echo $supplier->type === 'individual' ? 'selected' : ''; ?>>فرد (Individual)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace text-right" value="<?php echo htmlspecialchars($supplier->phone); ?>" style="direction: ltr;">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace text-right" value="<?php echo htmlspecialchars($supplier->email); ?>" style="direction: ltr;">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">العنوان الوطني / موقع المستودع</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($supplier->address); ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات وشروط التعامل</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($supplier->notes); ?></textarea>
                </div>
            </div>

            <div class="alert alert-warning mt-4 mb-0">
                <i class="fas fa-info-circle"></i> الرصيد المالي الحالي للمورد هو <strong><?php echo number_format($supplier->balance, 2); ?> ر.س</strong> ولا يمكن تعديله يدوياً من هنا. قم بتسجيل سند صرف أو فاتورة مشتريات لتعديله بشكل قانوني.
            </div>

            <div class="card-footer mt-4" style="margin: 0 -24px -24px; padding: 20px 24px;">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
                <a href="<?php echo URLROOT; ?>/supplier/index" class="btn btn-secondary">إلغاء</a>
            </div>
            
        </form>
    </div>
</div>