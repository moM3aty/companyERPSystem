<?php
// app/views/products/create.php
$categories = $data['categories'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-box-open text-primary"></i> إضافة صنف جديد للمخزون</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/product/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم المنتج أو الصنف <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="اسم الصنف كما يظهر في الفواتير" required>
                </div>

                <div class="form-group">
                    <label class="form-label">التصنيف</label>
                    <select name="category_id" class="form-control">
                        <option value="">-- بدون تصنيف --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رمز التخزين الداخلي (SKU) <span class="required">*</span></label>
                    <input type="text" name="sku" class="form-control font-monospace" required style="direction:ltr; text-align:right;" placeholder="مثال: PRD-001">
                </div>

                <div class="form-group">
                    <label class="form-label">الباركود الدولي (Barcode)</label>
                    <input type="text" name="barcode" class="form-control font-monospace" style="direction:ltr; text-align:right;" placeholder="رقم الباركود إن وجد">
                </div>

                <div class="form-group">
                    <label class="form-label">وحدة القياس <span class="required">*</span></label>
                    <select name="unit" class="form-control" required>
                        <option value="قطعة">قطعة</option>
                        <option value="كرتونة">كرتونة</option>
                        <option value="درزن">درزن (12 حبة)</option>
                        <option value="كجم">كيلوجرام</option>
                        <option value="لتر">لتر</option>
                        <option value="متر">متر</option>
                        <option value="خدمة">خدمة</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">سعر البيع للجمهور (ر.س) <span class="required">*</span></label>
                    <input type="number" name="price" step="0.01" class="form-control font-monospace text-success fw-bold" value="0.00" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">تكلفة الشراء (التكلفة) (ر.س)</label>
                    <input type="number" name="cost" step="0.01" class="form-control font-monospace text-danger fw-bold" value="0.00" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group border rounded p-3 bg-light">
                    <label class="form-label">الرصيد الافتتاحي (الكمية الحالية)</label>
                    <input type="number" name="quantity" class="form-control font-monospace fw-bold" value="0" style="direction:ltr; text-align:center; font-size: 18px;">
                </div>

                <div class="form-group border rounded p-3" style="background: #fffbeb; border-color: #fde68a !important;">
                    <label class="form-label text-warning">حد إعادة الطلب (تنبيه النواقص)</label>
                    <input type="number" name="reorder_point" class="form-control font-monospace" value="5" style="direction:ltr; text-align:center;">
                </div>

                <div class="form-group full-width mt-3">
                    <label class="d-flex align-items-center gap-2 p-3 border" style="background:#f0fdf4; border-color:#bbf7d0 !important; border-radius:var(--radius-sm); cursor:pointer;">
                        <input type="checkbox" name="track_batches" value="1" style="width: 20px; height: 20px; accent-color: var(--success);">
                        <div>
                            <span class="fw-bold text-success d-block">تفعيل تتبع السيريال والتشغيلات (Lots & Serial Numbers)</span>
                            <small class="text-muted">قم بتفعيل هذا الخيار للأدوية، الأغذية، والأجهزة الإلكترونية لتتبع تاريخ الصلاحية لكل قطعة.</small>
                        </div>
                    </label>
                </div>

                <div class="form-group full-width mt-3">
                    <label class="form-label">وصف المنتج (ملاحظات)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="اكتب وصفاً أو مواصفات إضافية للصنف..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الصنف الجديد</button>
            <a href="<?php echo URLROOT; ?>/product/index" class="btn btn-secondary">إلغاء الرجوع</a>
        </div>
    </form>
</div>