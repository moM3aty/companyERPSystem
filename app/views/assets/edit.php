<?php
// app/views/assets/edit.php
$asset = $data['asset'] ?? null;
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل الأصل: <?php echo htmlspecialchars($asset->name ?? ''); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/asset/edit/<?php echo $asset->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">اسم الأصل <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($asset->name ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">الرقم التسلسلي (Asset Tag)</label>
                    <input type="text" name="asset_tag" class="form-control font-monospace" style="direction:ltr; text-align:right;" value="<?php echo htmlspecialchars($asset->asset_tag ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">تصنيف الأصل <span class="required">*</span></label>
                    <select name="category" class="form-control" required>
                        <option value="equipment" <?php echo ($asset->category ?? '') == 'equipment' ? 'selected' : ''; ?>>معدات وأجهزة</option>
                        <option value="vehicle" <?php echo ($asset->category ?? '') == 'vehicle' ? 'selected' : ''; ?>>مركبات وسيارات</option>
                        <option value="furniture" <?php echo ($asset->category ?? '') == 'furniture' ? 'selected' : ''; ?>>أثاث ومفروشات</option>
                        <option value="real_estate" <?php echo ($asset->category ?? '') == 'real_estate' ? 'selected' : ''; ?>>عقارات ومباني</option>
                        <option value="other" <?php echo ($asset->category ?? '') == 'other' ? 'selected' : ''; ?>>أخرى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الشراء <span class="required">*</span></label>
                    <input type="date" name="purchase_date" class="form-control" value="<?php echo !empty($asset->purchase_date) ? date('Y-m-d', strtotime($asset->purchase_date)) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الموقع / القسم</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($asset->location ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">تكلفة الشراء (ر.س) <span class="required">*</span></label>
                    <input type="number" name="purchase_cost" step="0.01" min="1" class="form-control font-monospace text-primary fw-bold" required style="direction:ltr; text-align:right;" value="<?php echo $asset->purchase_cost ?? '0.00'; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">القيمة الخردة (Salvage)</label>
                    <input type="number" name="salvage_value" step="0.01" min="0" class="form-control font-monospace" style="direction:ltr; text-align:right;" value="<?php echo $asset->salvage_value ?? '0.00'; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">العمر الإنتاجي (سنوات) <span class="required">*</span></label>
                    <input type="number" name="useful_life_years" min="1" class="form-control font-monospace text-success fw-bold" required style="direction:ltr; text-align:right;" value="<?php echo $asset->useful_life_years ?? '1'; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo ($asset->status ?? '') == 'active' ? 'selected' : ''; ?>>مستخدم حالياً</option>
                        <option value="maintenance" <?php echo ($asset->status ?? '') == 'maintenance' ? 'selected' : ''; ?>>في الصيانة</option>
                        <option value="disposed" <?php echo ($asset->status ?? '') == 'disposed' ? 'selected' : ''; ?>>مُتلف</option>
                        <option value="sold" <?php echo ($asset->status ?? '') == 'sold' ? 'selected' : ''; ?>>مباع</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($asset->notes ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/asset/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>