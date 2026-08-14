<?php
// app/views/customer/create.php
?>
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-user-plus text-primary"></i> تسجيل عميل جديد</h3></div>
    <form action="<?php echo URLROOT; ?>/customer/create" method="POST">
        <div class="card-body">
            <!-- Basic Info -->
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fas fa-id-card"></i> بيانات العميل الأساسية</h5>
            <div class="form-grid mb-4">
                <div class="form-group"><label class="form-label">كود العميل (Auto)</label><input type="text" name="customer_number" class="form-control font-monospace bg-light" value="CUST-<?php echo time(); ?>" readonly></div>
                <div class="form-group"><label class="form-label">اسم العميل <span class="required">*</span></label><input type="text" name="name" class="form-control" required placeholder="اسم الشخص أو الممثل"></div>
                <div class="form-group"><label class="form-label">اسم الشركة (إن وجد)</label><input type="text" name="company_name" class="form-control" placeholder="اسم المؤسسة التجارية"></div>
                <div class="form-group"><label class="form-label">الرقم الضريبي (VAT)</label><input type="text" name="vat_number" class="form-control font-monospace" style="direction:ltr; text-align:right;"></div>
                <div class="form-group full-width"><label class="form-label">عنوان العميل (Address)</label><input type="text" name="address" class="form-control"></div>
            </div>

            <!-- Contact Info -->
            <h5 class="fw-bold text-info mb-3 border-bottom pb-2"><i class="fas fa-address-book"></i> بيانات التواصل</h5>
            <div class="form-grid mb-4 bg-light p-3 border rounded">
                <div class="form-group"><label class="form-label">مسؤول التواصل</label><input type="text" name="contact_person" class="form-control"></div>
                <div class="form-group"><label class="form-label">رقم الجوال / الهاتف</label><input type="text" name="phone" class="form-control font-monospace" style="direction:ltr; text-align:right;"></div>
                <div class="form-group full-width"><label class="form-label">البريد الإلكتروني</label><input type="email" name="email" class="form-control font-monospace" style="direction:ltr; text-align:right;"></div>
            </div>

            <!-- Financial Info -->
            <h5 class="fw-bold text-warning mb-3 border-bottom pb-2"><i class="fas fa-money-bill-trend-up"></i> البيانات والحدود المالية</h5>
            <div class="form-grid mb-2">
                <div class="form-group"><label class="form-label text-danger">الحد الائتماني (Credit Limit)</label><input type="number" step="0.01" name="credit_limit" class="form-control text-danger fw-bold font-monospace" value="0.00" style="direction:ltr;"></div>
                <div class="form-group"><label class="form-label text-success">الرصيد الافتتاحي المستحق</label><input type="number" step="0.01" name="opening_balance" class="form-control fw-bold text-success font-monospace" value="0.00" style="direction:ltr;"></div>
                <div class="form-group"><label class="form-label">شروط الدفع (Payment Terms)</label><input type="text" name="payment_terms" class="form-control" placeholder="مثال: نقدي، 15 يوم، 30 يوم"></div>
                <div class="form-group"><label class="form-label">عملة التعامل</label><input type="text" name="currency" class="form-control font-monospace text-center" value="SAR"></div>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ ملف العميل</button><a href="<?php echo URLROOT; ?>/customer/index" class="btn btn-secondary">إلغاء</a></div>
    </form>
</div>