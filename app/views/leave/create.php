<?php $flash = $data['flash'] ?? null; ?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/leave/create" method="POST">
            <div class="form-section">
                <div class="form-group">
                    <label>نوع الإجازة</label>
                    <select name="leave_type_id" class="form-input" required>
                        <option value="">-- اختر --</option>
                        <?php foreach ($data['leave_types'] as $type) : ?>
                            <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>تاريخ البداية</label>
                    <input type="date" name="start_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>تاريخ النهاية</label>
                    <input type="date" name="end_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>السبب</label>
                    <textarea name="reason" class="form-input" rows="3"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> تقديم الطلب</button>
                <a href="<?php echo URL_ROOT; ?>/leave/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>