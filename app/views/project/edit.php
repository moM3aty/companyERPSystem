<?php
// app/views/project/edit.php
$project = $data['project'] ?? null;
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-pen text-accent"></i> تعديل مشروع: <?php echo htmlspecialchars($project->name ?? ''); ?></h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/project/edit/<?php echo $project->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم المشروع <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($project->name ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">كود المشروع (مرجع)</label>
                    <input type="text" name="code" class="form-control font-monospace" value="<?php echo htmlspecialchars($project->code ?? ''); ?>" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group border rounded p-3 bg-light">
                    <label class="form-label text-success">الميزانية المخصصة (ر.س)</label>
                    <input type="number" name="budget" step="0.01" class="form-control font-monospace fw-bold text-success text-center" value="<?php echo $project->budget ?? '0.00'; ?>" style="font-size: 18px;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $project->start_date ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ التسليم</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $project->end_date ?? ''; ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">حالة المشروع</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="active" <?php echo ($project->status == 'active') ? 'selected' : ''; ?>>نشط (قيد العمل)</option>
                        <option value="on_hold" <?php echo ($project->status == 'on_hold') ? 'selected' : ''; ?>>معلق</option>
                        <option value="completed" <?php echo ($project->status == 'completed') ? 'selected' : ''; ?>>مكتمل</option>
                        <option value="cancelled" <?php echo ($project->status == 'cancelled') ? 'selected' : ''; ?>>ملغي</option>
                    </select>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">وصف المشروع</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($project->description ?? ''); ?></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/project/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>