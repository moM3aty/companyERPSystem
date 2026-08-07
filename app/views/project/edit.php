<?php
// app/views/project/edit.php
$project = $project ?? ($data['project'] ?? null);
$customers = $customers ?? ($data['customers'] ?? []);
$employees = $employees ?? ($data['employees'] ?? []);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل بيانات المشروع: <?php echo htmlspecialchars($project->name ?? ''); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/project/edit/<?php echo $project->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">اسم المشروع <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($project->name ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">كود المشروع (Code) <span class="required">*</span></label>
                    <input type="text" name="code" class="form-control font-monospace" value="<?php echo htmlspecialchars($project->code ?? ''); ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">العميل المرتبط</label>
                    <select name="customer_id" class="form-control">
                        <option value="">-- مشروع داخلي (بدون عميل) --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo ($project->customer_id ?? null) == $c->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البدء</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo !empty($project->start_date) ? date('Y-m-d', strtotime($project->start_date)) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ التسليم المتوقع</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo !empty($project->end_date) ? date('Y-m-d', strtotime($project->end_date)) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">الميزانية المخصصة (Budget)</label>
                    <input type="number" name="budget" step="0.01" min="0" class="form-control font-monospace text-success fw-bold" value="<?php echo $project->budget ?? '0.00'; ?>" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">حالة المشروع</label>
                    <select name="status" class="form-control">
                        <option value="planning" <?php echo ($project->status ?? '') == 'planning' ? 'selected' : ''; ?>>تخطيط</option>
                        <option value="active" <?php echo ($project->status ?? '') == 'active' ? 'selected' : ''; ?>>نشط قيد التنفيذ</option>
                        <option value="on_hold" <?php echo ($project->status ?? '') == 'on_hold' ? 'selected' : ''; ?>>معلق (On Hold)</option>
                        <option value="completed" <?php echo ($project->status ?? '') == 'completed' ? 'selected' : ''; ?>>مكتمل</option>
                        <option value="cancelled" <?php echo ($project->status ?? '') == 'cancelled' ? 'selected' : ''; ?>>ملغي</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">مدير المشروع (PM)</label>
                    <select name="project_manager" class="form-control">
                        <option value="">-- غير معين --</option>
                        <?php foreach($employees as $e): ?>
                            <option value="<?php echo $e->id; ?>" <?php echo ($project->project_manager ?? null) == $e->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($e->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">وصف ونطاق المشروع</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($project->description ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/project/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>