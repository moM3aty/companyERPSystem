<?php
// app/views/project/create.php
$flash = $data['flash'] ?? null;
$customers = $data['customers'] ?? [];
$employees = $data['employees'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/project/create" method="POST">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>اسم المشروع</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>الكود</label>
                        <input type="text" name="code" class="form-input" placeholder="مثل: PRJ-001" required>
                    </div>
                    <div class="form-group">
                        <label>العميل</label>
                        <select name="customer_id" class="form-input">
                            <option value="">-- بدون عميل --</option>
                            <?php foreach ($customers as $c) : ?>
                                <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>مدير المشروع</label>
                        <select name="project_manager" class="form-input">
                            <option value="">-- اختر --</option>
                            <?php foreach ($employees as $emp) : ?>
                                <option value="<?php echo $emp->id; ?>"><?php echo $emp->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>تاريخ البداية</label>
                        <input type="date" name="start_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>تاريخ النهاية</label>
                        <input type="date" name="end_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>الميزانية</label>
                        <input type="number" name="budget" class="form-input" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status" class="form-input">
                            <option value="planning">تخطيط</option>
                            <option value="active">نشط</option>
                            <option value="on_hold">متوقف مؤقتًا</option>
                            <option value="completed">مكتمل</option>
                            <option value="cancelled">ملغي</option>
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
                <a href="<?php echo URL_ROOT; ?>/project/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>