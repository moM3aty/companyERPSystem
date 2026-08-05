<?php
$flash = $data['flash'] ?? null;
$employees = $data['employees'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/attendance/create" method="POST">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>الموظف</label>
                        <select name="employee_id" class="form-input" required>
                            <option value="">-- اختر موظف --</option>
                            <?php foreach ($employees as $emp) : ?>
                                <option value="<?php echo $emp->id; ?>"><?php echo $emp->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>التاريخ</label>
                        <input type="date" name="date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>وقت الدخول</label>
                        <input type="time" name="check_in" class="form-input" value="08:00" required>
                    </div>
                    <div class="form-group">
                        <label>وقت الخروج</label>
                        <input type="time" name="check_out" class="form-input" value="17:00">
                    </div>
                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status" class="form-input" required>
                            <option value="present">حاضر</option>
                            <option value="absent">غائب</option>
                            <option value="late">متأخر</option>
                            <option value="leave">في إجازة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-input" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ</button>
                <a href="<?php echo URL_ROOT; ?>/attendance/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>