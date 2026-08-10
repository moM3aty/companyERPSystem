<?php
// app/views/recruitment/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-user-plus text-primary"></i> إضافة مرشح للتوظيف</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/recruitment/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم المرشح <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="الاسم الرباعي">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control font-monospace" style="direction:ltr;">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace" style="direction:ltr;">
                </div>

                <div class="form-group">
                    <label class="form-label text-primary">الوظيفة المتقدم لها <span class="required">*</span></label>
                    <input type="text" name="position_applied" class="form-control fw-bold text-primary" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الراتب المتوقع (Expected Salary)</label>
                    <input type="number" step="0.01" name="expected_salary" class="form-control font-monospace text-center" value="0.00" style="direction:ltr;">
                </div>

                <div class="form-group">
                    <label class="form-label">المرحلة الحالية للطلب</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="applied" selected>متقدم جديد (Applied)</option>
                        <option value="screening">فرز ومراجعة السيرة (Screening)</option>
                        <option value="interview">مقابلة شخصية (Interview)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label text-warning">تاريخ المقابلة (إن وُجد)</label>
                    <input type="datetime-local" name="interview_date" class="form-control">
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">ملاحظات المقابلة أو مهارات المرشح</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="أبرز المهارات، الانطباع العام..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> إضافة المرشح</button>
            <a href="<?php echo URLROOT; ?>/recruitment/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>