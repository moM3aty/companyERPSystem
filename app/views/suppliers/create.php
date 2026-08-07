<?php
// المسار: app/views/suppliers/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-truck-field" style="color: var(--primary);"></i> إضافة مورد جديد</h3>
    </div>
    
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/supplier/create" method="POST">
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">اسم المورد أو الشركة <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="مثال: شركة المراعي للتوزيع" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الشخص المسؤول (Contact Person)</label>
                    <input type="text" name="contact_person" class="form-control" placeholder="مثال: أ. أحمد محمود">
                </div>

                <div class="form-group">
                    <label class="form-label">نوع المورد <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="company">شركة (Company)</option>
                        <option value="individual">فرد (Individual)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace text-right" placeholder="05XXXXXXXX" style="direction: ltr;">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace text-right" placeholder="supplier@example.com" style="direction: ltr;">
                </div>

                <div class="form-group">
                    <label class="form-label">الرصيد الافتتاحي (دائن)</label>
                    <input type="number" name="balance" step="0.01" value="0.00" class="form-control font-monospace text-right" style="direction: ltr;">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">العنوان الوطني / موقع المستودع</label>
                    <input type="text" name="address" class="form-control" placeholder="المدينة، الحي، الشارع...">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات وشروط التعامل</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="أية ملاحظات عن سياسة الدفع، التوصيل، إلخ..."></textarea>
                </div>
            </div>

            <div class="card-footer mt-4" style="margin: 0 -24px -24px; padding: 20px 24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ المورد</button>
                <a href="<?php echo URLROOT; ?>/supplier/index" class="btn btn-secondary">إلغاء</a>
            </div>
            
        </form>
    </div>
</div>