<?php
// app/views/productBatch/create.php
$product = $data['product'] ?? null;
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark">
            <i class="fas fa-plus text-primary"></i> 
            تسجيل تشغيلة أو سيريال جديد
        </h3>
        <?php if($product): ?>
            <span class="badge badge-info fs-6"><?php echo htmlspecialchars($product->name); ?></span>
        <?php endif; ?>
    </div>

    <?php 
        $flash = Session::getFlash();
        if ($flash): 
    ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>" style="margin: 20px 20px 0 20px;">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/productBatch/create/<?php echo $product->id ?? ''; ?>" method="POST">
        <div class="card-body">
            
            <div class="alert alert-secondary mb-4">
                <i class="fas fa-info-circle"></i> يمكنك تسجيل "رقم التشغيلة" (Lot) لكمية كاملة، أو تسجيل "سيريال" (Serial) لكل قطعة على حدة.
            </div>

            <div class="form-grid">
                
                <div class="form-group">
                    <label class="form-label">رقم التشغيلة (Lot/Batch Number)</label>
                    <input type="text" name="lot_number" class="form-control font-monospace" placeholder="مثال: LOT-2023-A" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم السيريال (Serial Number)</label>
                    <input type="text" name="serial_number" class="form-control font-monospace" placeholder="رقم تسلسلي فريد" style="direction:ltr; text-align:right;">
                    <small class="text-muted">اتركه فارغاً إذا كنت تسجل تشغيلة كاملة وليس قطعة واحدة.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الإنتاج (Production Date)</label>
                    <input type="date" name="production_date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الانتهاء (Expiry Date)</label>
                    <input type="date" name="expiry_date" class="form-control">
                </div>

                <div class="form-group border rounded p-3 bg-light">
                    <label class="form-label">الكمية (Quantity) <span class="required">*</span></label>
                    <input type="number" name="quantity" class="form-control font-monospace fw-bold" value="1" required style="direction:ltr; text-align:center; font-size: 18px;">
                </div>

                <div class="form-group border rounded p-3">
                    <label class="form-label">حالة التشغيلة</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="active" selected>نشط / متاح للبيع</option>
                        <option value="damaged">تالف / غير صالح</option>
                        <option value="expired">منتهي الصلاحية</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات إضافية</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="ملاحظات حول هذه التشغيلة..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التشغيلة</button>
            <a href="<?php echo URLROOT; ?>/productBatch/index/<?php echo $product->id ?? ''; ?>" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>