<?php
// app/views/leads/create.php
$users = $data['users'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-plus text-primary"></i> إضافة عميل محتمل (Lead)</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/lead/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">اسم العميل <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الشركة / المؤسسة</label>
                    <input type="text" name="company" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">مصدر العميل (Source)</label>
                    <select name="source" class="form-control">
                        <option value="organic">بحث مباشر</option>
                        <option value="social_media">وسائل التواصل</option>
                        <option value="referral">إحالة / توصية</option>
                        <option value="website">الموقع الإلكتروني</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">الحالة المبدئية</label>
                    <select name="status" class="form-control">
                        <option value="new">جديد</option>
                        <option value="contacted">تم التواصل</option>
                        <option value="qualified">مؤهل (مهتم)</option>
                        <option value="lost">غير مهتم / ضائع</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">تعيين إلى (موظف المبيعات)</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- غير معين --</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?php echo $user->id; ?>"><?php echo htmlspecialchars($user->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">ملاحظات إضافية</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ العميل</button>
            <a href="<?php echo URLROOT; ?>/lead/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>