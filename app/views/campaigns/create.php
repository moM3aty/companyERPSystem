<?php
// المسار: app/views/campaigns/create.php
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-bullhorn"></i> إنشاء حملة تسويقية جديدة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/campaign/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم الحملة <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: حملة الجمعة البيضاء 2024">
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الحملة <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="social">وسائل التواصل الاجتماعي (Social Media)</option>
                        <option value="email">بريد إلكتروني (Email Marketing)</option>
                        <option value="sms">رسائل نصية (SMS)</option>
                        <option value="print">مطبوعات وإعلانات شوارع</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">حالة الحملة</label>
                    <select name="status" class="form-control">
                        <option value="planned">مخطط لها (Planned)</option>
                        <option value="active">نشطة حالياً (Active)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البداية <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ النهاية <span class="required">*</span></label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الميزانية المخصصة (ر.س) <span class="required">*</span></label>
                    <input type="number" name="budget" step="0.01" min="0" class="form-control font-monospace text-success fw-bold" value="0.00" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">الجمهور المستهدف</label>
                    <input type="text" name="target_audience" class="form-control" placeholder="مثال: عملاء الرياض، الشركات التقنية...">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الوصف والملاحظات</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="اكتب تفاصيل وأهداف هذه الحملة..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ وإطلاق الحملة</button>
            <a href="<?php echo URLROOT; ?>/campaign/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>