<?php
// app/views/leave/create.php
$employees = $data['employees'] ?? [];
$isAdmin = in_array(Session::getUserRole(), ['admin', 'manager', 'super_admin']);
?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تقديم طلب إجازة</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/leave/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <?php if($isAdmin && !empty($employees)): ?>
                <div class="form-group full-width">
                    <label class="form-label">الموظف (تُرك فارغاً للتقديم باسمك) <span class="required">*</span></label>
                    <select name="employee_id" class="form-control">
                        <option value="<?php echo Session::getUserId(); ?>">أنا (<?php echo Session::getUserName(); ?>)</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="form-group full-width">
                    <label class="form-label">نوع الإجازة <span class="required">*</span></label>
                    <select name="leave_type" class="form-control" required>
                        <option value="annual">إجازة سنوية (Annual)</option>
                        <option value="sick">إجازة مرضية (Sick)</option>
                        <option value="unpaid">إجازة بدون راتب (Unpaid)</option>
                        <option value="maternity">أمومة / أبوة</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البداية <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ النهاية <span class="required">*</span></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">السبب / ملاحظات</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="اذكر سبب الإجازة أو أية ملاحظات إضافية..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال الطلب للاعتماد</button>
            <a href="<?php echo URLROOT; ?>/leave/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>