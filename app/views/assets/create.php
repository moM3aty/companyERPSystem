<?php
// app/views/assets/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-building text-primary"></i> تسجيل أصل ثابت جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/asset/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">اسم الأصل <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="سيارة تويوتا، لابتوب ماك...">
                </div>

                <div class="form-group">
                    <label class="form-label">الرقم التسلسلي (Asset Tag)</label>
                    <input type="text" name="asset_tag" class="form-control font-monospace" style="direction:ltr; text-align:right;" placeholder="يُترك فارغاً للإنشاء التلقائي">
                </div>

                <div class="form-group">
                    <label class="form-label">تصنيف الأصل <span class="required">*</span></label>
                    <select name="category" class="form-control" required>
                        <option value="equipment">معدات وأجهزة</option>
                        <option value="vehicle">مركبات وسيارات</option>
                        <option value="furniture">أثاث ومفروشات</option>
                        <option value="real_estate">عقارات ومباني</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الشراء <span class="required">*</span></label>
                    <input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الموقع / القسم</label>
                    <input type="text" name="location" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">تكلفة الشراء (ر.س) <span class="required">*</span></label>
                    <input type="number" name="purchase_cost" step="0.01" min="1" class="form-control font-monospace text-primary fw-bold" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">القيمة الخردة (Salvage)</label>
                    <input type="number" name="salvage_value" step="0.01" min="0" value="0.00" class="form-control font-monospace" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">العمر الإنتاجي (سنوات) <span class="required">*</span></label>
                    <input type="number" name="useful_life_years" min="1" value="5" class="form-control font-monospace text-success fw-bold" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control">
                        <option value="active">مستخدم حالياً</option>
                        <option value="maintenance">في الصيانة</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الأصل</button>
            <a href="<?php echo URLROOT; ?>/asset/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>