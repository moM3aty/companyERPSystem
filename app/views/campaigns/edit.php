<?php
// المسار: app/views/campaigns/edit.php
$camp = $campaign ?? ($data['campaign'] ?? null);
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل بيانات الحملة: <?php echo htmlspecialchars($camp->name); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/campaign/edit/<?php echo $camp->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم الحملة <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($camp->name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الحملة <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="social" <?php echo $camp->type == 'social' ? 'selected' : ''; ?>>وسائل التواصل الاجتماعي</option>
                        <option value="email" <?php echo $camp->type == 'email' ? 'selected' : ''; ?>>بريد إلكتروني</option>
                        <option value="sms" <?php echo $camp->type == 'sms' ? 'selected' : ''; ?>>رسائل نصية (SMS)</option>
                        <option value="print" <?php echo $camp->type == 'print' ? 'selected' : ''; ?>>مطبوعات</option>
                        <option value="other" <?php echo $camp->type == 'other' ? 'selected' : ''; ?>>أخرى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الحملة</label>
                    <select name="status" class="form-control">
                        <option value="planned" <?php echo $camp->status == 'planned' ? 'selected' : ''; ?>>مخطط لها</option>
                        <option value="active" <?php echo $camp->status == 'active' ? 'selected' : ''; ?>>نشطة حالياً</option>
                        <option value="completed" <?php echo $camp->status == 'completed' ? 'selected' : ''; ?>>مكتملة</option>
                        <option value="cancelled" <?php echo $camp->status == 'cancelled' ? 'selected' : ''; ?>>ملغاة</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البداية <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($camp->start_date)); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ النهاية <span class="required">*</span></label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($camp->end_date)); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الميزانية المخصصة (ر.س) <span class="required">*</span></label>
                    <input type="number" name="budget" step="0.01" min="0" class="form-control font-monospace text-success fw-bold" value="<?php echo $camp->budget; ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">الجمهور المستهدف</label>
                    <input type="text" name="target_audience" class="form-control" value="<?php echo htmlspecialchars($camp->target_audience ?? ''); ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الوصف والملاحظات</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($camp->description ?? ''); ?></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/campaign/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>