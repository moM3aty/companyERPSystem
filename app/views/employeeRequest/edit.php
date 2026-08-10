<?php
// app/views/employeeRequest/edit.php
$req = $data['request']?? [];
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-reply"></i> الرد على طلب الموظف (HR Action)</h3>
    </div>
    <form action="<?php echo URLROOT; ?>/employeeRequest/edit/<?php echo $req->id; ?>" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="p-3 bg-light rounded border mb-3">
                <div class="text-muted" style="font-size:12px;">نوع الطلب: <strong class="text-dark"><?php echo htmlspecialchars($req->request_type); ?></strong></div>
                <div class="mt-2 text-dark" style="font-size:14px;">"<?php echo nl2br(htmlspecialchars($req->details)); ?>"</div>
            </div>

            <div class="form-group">
                <label class="form-label text-danger">قرار الإدارة (Status) <span class="required">*</span></label>
                <select name="status" class="form-control fw-bold" required>
                    <option value="pending" <?php echo $req->status == 'pending' ? 'selected' : ''; ?>>قيد الانتظار / المراجعة</option>
                    <option value="approved" <?php echo $req->status == 'approved' ? 'selected' : ''; ?>>موافق عليه (Approved)</option>
                    <option value="rejected" <?php echo $req->status == 'rejected' ? 'selected' : ''; ?>>مرفوض (Rejected)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">ملاحظات الإدارة (HR Notes)</label>
                <textarea name="hr_notes" class="form-control" rows="4" placeholder="هذا الرد سيظهر للموظف..."><?php echo htmlspecialchars($req->hr_notes ?? ''); ?></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-warning"><i class="fas fa-check-circle"></i> حفظ وتحديث الحالة</button></div>
    </form>
</div>