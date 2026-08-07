<?php
// المسار: app/views/customers/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-plus text-primary"></i> تسجيل عميل جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/customer/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم العميل / المؤسسة <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: شركة الأفق المحدودة">
                </div>

                <div class="form-group">
                    <label class="form-label">نوع العميل <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="individual">فرد (Individual)</option>
                        <option value="company">شركة (Company)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الرصيد الافتتاحي (ديون سابقة)</label>
                    <input type="number" name="balance" step="0.01" value="0.00" class="form-control font-monospace text-danger fw-bold" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace" placeholder="05XXXXXXXX" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace" placeholder="customer@example.com" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">العنوان التفصيلي</label>
                    <input type="text" name="address" class="form-control" placeholder="المدينة، الحي، الشارع، المبنى...">
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ العميل</button>
            <a href="<?php echo URLROOT; ?>/customer/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>