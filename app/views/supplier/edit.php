<?php
// app/views/supplier/edit.php
$s = $data['supplier'] ?? null;
?>
<div class="card" style="max-width: 800px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-edit"></i> تعديل بيانات المورد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/supplier/edit/<?php echo $s->id; ?>" method="POST">
        <div class="card-body form-grid">
            
            <div class="form-group full-width">
                <label class="form-label text-primary fw-bold">اسم الشركة أو المورد <span class="required">*</span></label>
                <input type="text" name="company_name" class="form-control fw-bold" required value="<?php echo htmlspecialchars($s->company_name); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">الشخص المسؤول</label>
                <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($s->contact_person); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control font-monospace" value="<?php echo htmlspecialchars($s->phone); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control font-monospace" value="<?php echo htmlspecialchars($s->email); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">الرقم الضريبي (VAT)</label>
                <input type="text" name="tax_number" class="form-control font-monospace" value="<?php echo htmlspecialchars($s->tax_number); ?>">
            </div>

            <!-- الرصيد في التعديل يكون للقراءة فقط، التحديث يتم عبر السندات والفواتير -->
            <div class="form-group full-width p-3 bg-light border rounded">
                <label class="form-label text-danger fw-bold">الرصيد الحالي (SAR)</label>
                <input type="text" class="form-control font-monospace fs-4 fw-bold text-danger text-center bg-white" value="<?php echo number_format($s->current_balance ?? ($s->balance ?? 0), 2); ?>" readonly disabled style="direction:ltr;">
                <small class="text-muted d-block mt-1 text-center">لتغيير الرصيد، يرجى إصدار سندات صرف أو إضافة فواتير مشتريات.</small>
            </div>

            <div class="form-group full-width">
                <label class="form-label">العنوان</label>
                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($s->address); ?>">
            </div>

            <div class="form-group full-width">
                <label class="form-label">ملاحظات عامة</label>
                <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($s->notes); ?></textarea>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-warning fw-bold px-5"><i class="fas fa-sync"></i> تحديث البيانات</button> 
            <a href="<?php echo URLROOT; ?>/supplier/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>