<?php
// app/views/exitProcess/edit.php
$exit = $data['exit']?? null;
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-dark text-white"><h3 class="card-title text-white mb-0"><i class="fas fa-clipboard-list"></i> المخالصة وإخلاء الطرف للموظف</h3></div>
    <form action="<?php echo URLROOT; ?>/exitProcess/edit/<?php echo $exit->id; ?>" method="POST">
        <div class="card-body form-grid">
            <div class="form-group full-width border-bottom pb-3">
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="assets_returned" <?php echo $exit->assets_returned ? 'checked' : ''; ?>> تسليم تمامی العهد والأصول الخاصة بالشركة</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="accounts_disabled" <?php echo $exit->accounts_disabled ? 'checked' : ''; ?>> إيقاف تمامی حسابات النظام والبريد الإلكتروني</label>
                <label class="d-flex align-items-center gap-2 p-2"><input type="checkbox" name="clearance_status" <?php echo $exit->clearance_status ? 'checked' : ''; ?>> المخالصة مع كافة الأقسام (المالية، الإدارة)</label>
            </div>
            <div class="form-group"><label class="form-label text-success">الراتب الأخير (ر.س)</label><input type="number" step="0.01" name="final_salary" class="form-control font-monospace text-success" value="<?php echo $exit->final_salary; ?>"></div>
            <div class="form-group"><label class="form-label text-primary">مكافأة نهاية الخدمة (ر.س)</label><input type="number" step="0.01" name="eos_calculation" class="form-control font-monospace text-primary" value="<?php echo $exit->eos_calculation; ?>"></div>
            <div class="form-group"><label class="form-label">رصيد الإجازات المتبقي</label><input type="number" step="0.01" name="leave_balance" class="form-control font-monospace" value="<?php echo $exit->leave_balance; ?>"></div>
            <div class="form-group full-width"><label class="form-label">ملاحظات مقابلة الخروج (Exit Interview)</label><textarea name="exit_interview" class="form-control" rows="3"><?php echo htmlspecialchars($exit->exit_interview ?? ''); ?></textarea></div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-dark"><i class="fas fa-lock"></i> اعتماد الإخلاء (سيتم تحويل الموظف لـ Terminated)</button></div>
    </form>
</div>