<?php
// app/views/supplier/create.php
?>
<div class="card" style="max-width: 800px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-plus-circle text-primary"></i> إضافة مورد جديد</h3>
    </div>
    
    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?> m-3 mb-0"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/supplier/create" method="POST">
        <div class="card-body form-grid">
            
            <div class="form-group full-width">
                <label class="form-label text-primary fw-bold">اسم الشركة أو المورد <span class="required">*</span></label>
                <input type="text" name="company_name" class="form-control fw-bold" required placeholder="مثال: شركة المراعي للتجارة">
            </div>

            <div class="form-group">
                <label class="form-label">الشخص المسؤول (للتواصل)</label>
                <input type="text" name="contact_person" class="form-control" placeholder="اسم المندوب أو المدير">
            </div>

            <div class="form-group">
                <label class="form-label">رقم الهاتف / الجوال</label>
                <input type="text" name="phone" class="form-control font-monospace" placeholder="05xxxxxxxx">
            </div>

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control font-monospace" placeholder="example@domain.com">
            </div>

            <div class="form-group">
                <label class="form-label">الرقم الضريبي (VAT)</label>
                <input type="text" name="tax_number" class="form-control font-monospace" placeholder="الرقم الضريبي الموحد">
            </div>

            <div class="form-group full-width p-3 bg-light border rounded">
                <label class="form-label text-danger fw-bold">الرصيد الافتتاحي (SAR)</label>
                <input type="number" step="0.01" name="current_balance" class="form-control font-monospace fs-4 fw-bold text-danger text-center" value="0.00" style="direction:ltr;">
                <small class="text-muted d-block mt-1">أدخل المبلغ إذا كان للمورد ديون سابقة عليكم. اتركه 0 إذا لم يكن هناك رصيد افتتاحي.</small>
            </div>

            <div class="form-group full-width">
                <label class="form-label">العنوان الوطني / التفصيلي</label>
                <input type="text" name="address" class="form-control" placeholder="المدينة، الحي، الشارع...">
            </div>

            <div class="form-group full-width">
                <label class="form-label">ملاحظات عامة</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="أي بيانات إضافية عن المورد..."></textarea>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary fw-bold px-5"><i class="fas fa-save"></i> حفظ بيانات المورد</button> 
            <a href="<?php echo URLROOT; ?>/supplier/index" class="btn btn-secondary">إلغاء الرجوع</a>
        </div>
    </form>
</div>