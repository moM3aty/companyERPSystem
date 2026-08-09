<?php
// app/views/suppliers/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تسجيل مورد جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/supplier/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم المورد أو الشركة <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: شركة التوريدات الحديثة">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace" style="direction:ltr; text-align:right;" placeholder="+966 5X XXX XXXX">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace" style="direction:ltr; text-align:right;" placeholder="info@supplier.com">
                </div>

                <div class="form-group">
                    <label class="form-label">الرقم الضريبي (VAT)</label>
                    <input type="text" name="tax_number" class="form-control font-monospace" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label text-primary">الرصيد الافتتاحي (ر.س)</label>
                    <input type="number" step="0.01" name="balance" class="form-control font-monospace fw-bold text-primary" value="0.00" style="direction:ltr; text-align:right;">
                    <small class="text-muted d-block mt-1">المبالغ المستحقة للمورد إن وجدت.</small>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">العنوان الوطني / ملاحظات</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="المدينة، الحي، الشارع، أو أية تفاصيل أخرى..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ المورد</button>
            <a href="<?php echo URLROOT; ?>/supplier/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>