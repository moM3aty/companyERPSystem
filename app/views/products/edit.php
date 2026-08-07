<?php
// المسار: app/views/products/edit.php
$product = $product ?? ($data['product'] ?? null);
$categories = $data['categories'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل بيانات الصنف: <?php echo htmlspecialchars($product->name); ?></h3>
    </div>
    
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/product/edit/<?php echo $product->id; ?>" method="POST">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم المنتج <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product->name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">التصنيف</label>
                    <select name="category_id" class="form-control">
                        <option value="">-- بدون تصنيف --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo $product->category_id == $c->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رمز التخزين (SKU) <span class="required">*</span></label>
                    <input type="text" name="sku" class="form-control font-monospace" value="<?php echo htmlspecialchars($product->sku); ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">الباركود (Barcode)</label>
                    <input type="text" name="barcode" class="form-control font-monospace" value="<?php echo htmlspecialchars($product->barcode); ?>" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">وحدة القياس <span class="required">*</span></label>
                    <select name="unit" class="form-control" required>
                        <option value="قطعة" <?php echo $product->unit == 'قطعة' ? 'selected' : ''; ?>>قطعة</option>
                        <option value="كرتونة" <?php echo $product->unit == 'كرتونة' ? 'selected' : ''; ?>>كرتونة</option>
                        <option value="كجم" <?php echo $product->unit == 'كجم' ? 'selected' : ''; ?>>كيلوجرام</option>
                        <option value="لتر" <?php echo $product->unit == 'لتر' ? 'selected' : ''; ?>>لتر</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">سعر البيع (ر.س) <span class="required">*</span></label>
                    <input type="number" name="price" step="0.01" class="form-control font-monospace text-success fw-bold" value="<?php echo $product->price; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الرصيد الفعلي (لا ينصح بتعديله يدوياً)</label>
                    <input type="number" name="quantity" class="form-control font-monospace" value="<?php echo $product->quantity; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">حد إعادة الطلب</label>
                    <input type="number" name="reorder_point" class="form-control font-monospace text-danger" value="<?php echo $product->reorder_point; ?>">
                </div>

                <div class="form-group full-width mt-2">
                    <label class="d-flex align-items-center gap-2 p-3" style="background:var(--page-bg); border-radius:var(--radius-sm); cursor:pointer;">
                        <input type="checkbox" name="track_batches" value="1" <?php echo $product->track_batches ? 'checked' : ''; ?>>
                        <span class="fw-bold">تتبع أرقام التشغيلة وتواريخ الصلاحية</span>
                    </label>
                </div>

            </div>
            
            <div class="card-footer mt-4" style="margin: 0 -24px -24px; padding: 20px 24px;">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
                <a href="<?php echo URLROOT; ?>/product/index" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>