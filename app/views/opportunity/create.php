<?php
$flash = $data['flash'] ?? null;
$customers = $data['customers'] ?? [];
$users = $data['users'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/opportunity/create" method="POST">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="title" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>العميل</label>
                        <select name="customer_id" class="form-input" required>
                            <option value="">-- اختر --</option>
                            <?php foreach ($customers as $c) : ?>
                                <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>المرحلة</label>
                        <select name="stage" class="form-input">
                            <option value="qualification">تأهيل</option>
                            <option value="needs_analysis">تحليل الاحتياجات</option>
                            <option value="proposal">عرض سعر</option>
                            <option value="negotiation">تفاوض</option>
                            <option value="closed_won">مغلق - فوز</option>
                            <option value="closed_lost">مغلق - خسارة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>القيمة التقديرية</label>
                        <input type="number" name="estimated_value" class="form-input" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>نسبة الاحتمال</label>
                        <input type="number" name="probability" class="form-input" min="0" max="100" value="50">
                    </div>
                    <div class="form-group">
                        <label>تاريخ الإغلاق المتوقع</label>
                        <input type="date" name="expected_close_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>المسؤول</label>
                        <select name="assigned_to" class="form-input">
                            <option value="">-- غير محدد --</option>
                            <?php foreach ($users as $u) : ?>
                                <option value="<?php echo $u->id; ?>"><?php echo $u->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الوصف</label>
                        <textarea name="description" class="form-input" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ</button>
                <a href="<?php echo URL_ROOT; ?>/opportunity/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>