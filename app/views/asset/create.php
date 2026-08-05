<?php $flash = $data['flash'] ?? null; ?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/asset/create" method="POST">
            <div class="form-section">
                <h3>بيانات الأصل</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>اسم الأصل <span class="req">*</span></label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>كود الأصل <span class="req">*</span></label>
                        <input type="text" name="asset_code" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>التصنيف</label>
                        <input type="text" name="category" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>تاريخ الشراء <span class="req">*</span></label>
                        <input type="date" name="purchase_date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>سعر الشراء (ر.س) <span class="req">*</span></label>
                        <input type="number" name="purchase_price" class="form-input" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>قيمة الخردة (ر.س)</label>
                        <input type="number" name="salvage_value" class="form-input" step="0.01" value="0">
                    </div>
                    <div class="form-group">
                        <label>العمر الإنتاجي (سنوات)</label>
                        <input type="number" name="useful_life_years" class="form-input" value="5">
                    </div>
                    <div class="form-group">
                        <label>طريقة الإهلاك</label>
                        <select name="depreciation_method" class="form-input">
                            <option value="straight_line">القسط الثابت</option>
                            <option value="declining_balance">القسط المتناقص</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>مخصص لـ</label>
                        <select name="assigned_to" class="form-input">
                            <option value="">-- غير مخصص --</option>
                            <?php foreach ($data['employees'] as $emp) : ?>
                                <option value="<?php echo $emp->id; ?>"><?php echo $emp->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الموقع</label>
                        <input type="text" name="location" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>ملاحظات</label>
                    <textarea name="notes" class="form-input" rows="2"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ</button>
                <a href="<?php echo URL_ROOT; ?>/asset/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>