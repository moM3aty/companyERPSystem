<?php
// app/views/contract/create.php
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> صياغة وإضافة عقد جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/contract/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">موضوع العقد / العنوان <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="مثال: عقد صيانة أجهزة سنوي">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم العقد (مرجع) <span class="required">*</span></label>
                    <input type="text" name="contract_number" class="form-control font-monospace" value="<?php echo $data['default_number'] ?? ''; ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">الطرف الثاني (اسم العميل / المؤسسة)</label>
                    <input type="text" name="customer_name" class="form-control" placeholder="اسم الجهة المتعاقد معها">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البداية (سريان العقد)</label>
                    <input type="date" name="start_date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" class="form-control">
                </div>

                <div class="form-group border rounded p-3 bg-light">
                    <label class="form-label text-success">القيمة الإجمالية للعقد (ر.س)</label>
                    <input type="number" name="value" step="0.01" class="form-control font-monospace fw-bold text-success text-center" value="0.00" style="font-size: 18px;">
                </div>

                <div class="form-group border rounded p-3">
                    <label class="form-label">حالة العقد</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="draft" selected>مسودة (Draft)</option>
                        <option value="pending">قيد الانتظار (توقيع)</option>
                        <option value="active">ساري المفعول (Active)</option>
                        <option value="expired">منتهي (Expired)</option>
                        <option value="terminated">مفسوخ / ملغي</option>
                    </select>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">نص العقد (الشروط والأحكام)</label>
                    <textarea name="description" class="form-control" rows="8" placeholder="قم بكتابة أو لصق بنود العقد هنا..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ مسودة العقد</button>
            <a href="<?php echo URLROOT; ?>/contract/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>