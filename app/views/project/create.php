<?php
// app/views/project/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-plus text-primary"></i> إضافة مشروع جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/project/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">اسم المشروع <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: تطوير تطبيق الموارد البشرية">
                </div>

                <div class="form-group">
                    <label class="form-label">كود المشروع (مرجع)</label>
                    <input type="text" name="code" class="form-control font-monospace" placeholder="مثال: PRJ-2023-01" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group border rounded p-3 bg-light">
                    <label class="form-label text-success">الميزانية المخصصة (ر.س)</label>
                    <input type="number" name="budget" step="0.01" class="form-control font-monospace fw-bold text-success text-center" value="0.00" style="font-size: 18px;">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ البداية المتوقع</label>
                    <input type="date" name="start_date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ التسليم النهائي</label>
                    <input type="date" name="end_date" class="form-control">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">حالة المشروع</label>
                    <select name="status" class="form-control fw-bold">
                        <option value="active" selected>نشط (قيد العمل)</option>
                        <option value="on_hold">معلق</option>
                        <option value="completed">مكتمل</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">وصف المشروع / نطاق العمل</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="تفاصيل إضافية..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ وإنشاء المشروع</button>
            <a href="<?php echo URLROOT; ?>/project/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>