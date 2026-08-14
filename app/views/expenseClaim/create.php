<?php
// app/views/expenseClaim/create.php
$employees = $data['employees'] ?? [];
$projects = $data['projects'] ?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning"><h3 class="card-title text-warning-dark mb-0"><i class="fas fa-receipt"></i> تقديم مطالبة مصروفات موظف</h3></div>
    <form action="<?php echo URLROOT; ?>/expenseClaim/create" method="POST" enctype="multipart/form-data">
        <div class="card-body form-grid">
            <div class="form-group">
                <label class="form-label">الموظف (طالب الاسترداد) <span class="required">*</span></label>
                <select name="employee_id" class="form-control fw-bold" required>
                    <option value="">-- حدد الموظف --</option>
                    <?php foreach($employees as $e): ?><option value="<?php echo $e->id; ?>"><?php echo htmlspecialchars($e->full_name ?? $e->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">تاريخ المطالبة</label><input type="date" name="claim_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
            
            <div class="form-group">
                <label class="form-label">نوع المصروف (Expense Type)</label>
                <select name="expense_type" class="form-control fw-bold text-dark" required>
                    <option value="Business Travel">سفر وانتداب (Travel)</option>
                    <option value="Meals & Entertainment">ضيافة ووجبات</option>
                    <option value="Transportation">مواصلات ووقود</option>
                    <option value="Office Supplies">مشتريات مكتبية</option>
                    <option value="Other Petty Cash">نثريات أخرى</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">المشروع المرتبط</label>
                <select name="project_id" class="form-control">
                    <option value="">-- عام --</option>
                    <?php foreach($projects as $p): ?><option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group"><label class="form-label text-danger">المبلغ (بدون ضريبة)</label><input type="number" step="0.01" name="amount" class="form-control font-monospace fw-bold text-danger fs-5" required value="0.00" style="direction:ltr;"></div>
            <div class="form-group"><label class="form-label">الضريبة (VAT)</label><input type="number" step="0.01" name="vat_amount" class="form-control font-monospace fw-bold" value="0.00" style="direction:ltr;"></div>

            <div class="form-group full-width"><label class="form-label">الغرض من المصروف (Business Purpose) <span class="required">*</span></label><textarea name="business_purpose" class="form-control" rows="2" required placeholder="اشرح تفاصيل المصروف..."></textarea></div>
            <div class="form-group full-width border p-3 rounded bg-light mt-2"><label class="form-label text-primary"><i class="fas fa-camera"></i> إرفاق الإيصال / الفاتورة (Receipt)</label><input type="file" name="receipt_attachment" class="form-control bg-white"></div>
        </div>
        <div class="card-footer bg-light"><button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane"></i> تقديم للاعتماد</button> <a href="<?php echo URLROOT; ?>/expenseClaim/index" class="btn btn-secondary">إلغاء</a></div>
    </form>
</div>