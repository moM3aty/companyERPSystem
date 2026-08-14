<?php
// app/views/customer/edit.php
$c = $data['customer'] ?? null;
?>
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning"><h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل بيانات العميل</h3></div>
    <form action="<?php echo URLROOT; ?>/customer/edit/<?php echo $c->id; ?>" method="POST">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fas fa-id-card"></i> بيانات العميل الأساسية</h5>
            <div class="form-grid mb-4">
                <div class="form-group"><label class="form-label">كود العميل</label><input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($c->customer_number); ?>" disabled></div>
                <div class="form-group"><label class="form-label">اسم العميل <span class="required">*</span></label><input type="text" name="name" class="form-control fw-bold" value="<?php echo htmlspecialchars($c->name); ?>" required></div>
                <div class="form-group"><label class="form-label">اسم الشركة</label><input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($c->company_name ?? ''); ?>"></div>
                <div class="form-group"><label class="form-label">الرقم الضريبي (VAT)</label><input type="text" name="vat_number" class="form-control font-monospace" value="<?php echo htmlspecialchars($c->vat_number ?? ''); ?>" style="direction:ltr; text-align:right;"></div>
                <div class="form-group full-width"><label class="form-label">عنوان العميل</label><input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($c->address ?? ''); ?>"></div>
            </div>

            <h5 class="fw-bold text-info mb-3 border-bottom pb-2"><i class="fas fa-address-book"></i> بيانات التواصل</h5>
            <div class="form-grid mb-4 bg-light p-3 border rounded">
                <div class="form-group"><label class="form-label">مسؤول التواصل</label><input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($c->contact_person ?? ''); ?>"></div>
                <div class="form-group"><label class="form-label">رقم الجوال</label><input type="text" name="phone" class="form-control font-monospace" value="<?php echo htmlspecialchars($c->phone ?? ''); ?>" style="direction:ltr; text-align:right;"></div>
                <div class="form-group full-width"><label class="form-label">البريد الإلكتروني</label><input type="email" name="email" class="form-control font-monospace" value="<?php echo htmlspecialchars($c->email ?? ''); ?>" style="direction:ltr; text-align:right;"></div>
            </div>

            <h5 class="fw-bold text-warning mb-3 border-bottom pb-2"><i class="fas fa-money-bill-trend-up"></i> البيانات والحدود المالية</h5>
            <div class="form-grid mb-2">
                <div class="form-group"><label class="form-label text-danger">الحد الائتماني (Credit Limit)</label><input type="number" step="0.01" name="credit_limit" class="form-control text-danger fw-bold font-monospace" value="<?php echo $c->credit_limit; ?>" style="direction:ltr;"></div>
                <div class="form-group"><label class="form-label">شروط الدفع</label><input type="text" name="payment_terms" class="form-control" value="<?php echo htmlspecialchars($c->payment_terms ?? ''); ?>"></div>
                <div class="form-group"><label class="form-label">عملة التعامل</label><input type="text" name="currency" class="form-control font-monospace text-center" value="<?php echo htmlspecialchars($c->currency); ?>"></div>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button><a href="<?php echo URLROOT; ?>/customer/index" class="btn btn-secondary">إلغاء</a></div>
    </form>
</div>