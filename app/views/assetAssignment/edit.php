<?php
// app/views/assetAssignment/edit.php
$asset = $data['asset'] ?? null;
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning"><h3 class="card-title text-warning-dark mb-0"><i class="fas fa-exchange-alt"></i> تحديث العهدة / إرجاع الأصل</h3></div>
    <form action="<?php echo URLROOT; ?>/assetAssignment/edit/<?php echo $asset->id; ?>" method="POST">
        <div class="card-body form-grid">
            <div class="form-group full-width">
                <label class="form-label">الموظف</label>
                <select name="employee_id" class="form-control" required>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>" <?php echo $emp->id == $asset->employee_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">نوع العهدة</label><input type="text" name="asset_type" class="form-control" value="<?php echo htmlspecialchars($asset->asset_type); ?>" required></div>
            <div class="form-group"><label class="form-label">السيريال</label><input type="text" name="asset_id" class="form-control font-monospace" value="<?php echo htmlspecialchars($asset->asset_id); ?>" required></div>
            <div class="form-group"><label class="form-label">تاريخ التسليم</label><input type="date" name="issue_date" class="form-control" value="<?php echo $asset->issue_date; ?>"></div>
            <div class="form-group"><label class="form-label">الحالة عند التسليم</label><input type="text" name="condition_given" class="form-control" value="<?php echo htmlspecialchars($asset->condition_given ?? ''); ?>"></div>
            
            <div class="form-group border border-danger p-2 rounded bg-light mt-3">
                <label class="form-label text-danger">تاريخ الإرجاع (Return Date)</label>
                <input type="date" name="return_date" class="form-control font-monospace text-danger fw-bold" value="<?php echo $asset->return_date ?? ''; ?>">
            </div>
            <div class="form-group border border-primary p-2 rounded bg-light mt-3">
                <label class="form-label text-primary">الحالة الحالية (Status)</label>
                <select name="status" class="form-control fw-bold text-primary">
                    <option value="Assigned" <?php echo $asset->status == 'Assigned' ? 'selected' : ''; ?>>في حوزة الموظف (Assigned)</option>
                    <option value="Returned" <?php echo $asset->status == 'Returned' ? 'selected' : ''; ?>>مُسترجعة (Returned)</option>
                    <option value="Damaged" <?php echo $asset->status == 'Damaged' ? 'selected' : ''; ?>>تالفة (Damaged)</option>
                    <option value="Lost" <?php echo $asset->status == 'Lost' ? 'selected' : ''; ?>>مفقودة (Lost)</option>
                </select>
            </div>
            
            <div class="form-group full-width"><label class="form-label">ملاحظات (Notes)</label><textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($asset->notes ?? ''); ?></textarea></div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التحديثات</button></div>
    </form>
</div>