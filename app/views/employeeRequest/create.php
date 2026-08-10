<?php
// app/views/employeeRequest/create.php
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> تقديم طلب جديد للإدارة</h3></div>
    <form action="<?php echo URLROOT; ?>/employeeRequest/create" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="form-group">
                <label class="form-label">الموظف <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">نوع الطلب <span class="required">*</span></label>
                <select name="request_type" class="form-control fw-bold" required>
                    <option value="Salary Certificate">طلب تعريف بالراتب (Salary Certificate)</option>
                    <option value="Employment Certificate">شهادة خبرة / عمل</option>
                    <option value="Expense Reimbursement">استعاضة مصاريف (Expense Claim)</option>
                    <option value="Overtime Approval">اعتماد ساعات إضافية (Overtime)</option>
                    <option value="Attendance Correction">تعديل سجل حضور/انصراف</option>
                    <option value="General HR Inquiry">استفسار عام للإدارة</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">تفاصيل ومبررات الطلب <span class="required">*</span></label>
                <textarea name="details" class="form-control" rows="5" required placeholder="اشرح طلبك بالتفصيل لتسهيل الرد..."></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال للإدارة</button></div>
    </form>
</div>