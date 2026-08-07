<?php
// app/views/leads/edit.php
$lead = $lead ?? ($data['lead'] ?? null);
$users = $users ?? ($data['users'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل العميل المحتمل: <?php echo htmlspecialchars($lead->name); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/lead/edit/<?php echo $lead->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">اسم العميل <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($lead->name); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الشركة / المؤسسة</label>
                    <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($lead->company); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace" value="<?php echo htmlspecialchars($lead->phone); ?>" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace" value="<?php echo htmlspecialchars($lead->email); ?>" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">مصدر العميل (Source)</label>
                    <select name="source" class="form-control">
                        <option value="organic" <?php echo $lead->source == 'organic' ? 'selected' : ''; ?>>بحث مباشر</option>
                        <option value="social_media" <?php echo $lead->source == 'social_media' ? 'selected' : ''; ?>>وسائل التواصل</option>
                        <option value="referral" <?php echo $lead->source == 'referral' ? 'selected' : ''; ?>>إحالة / توصية</option>
                        <option value="website" <?php echo $lead->source == 'website' ? 'selected' : ''; ?>>الموقع الإلكتروني</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control">
                        <option value="new" <?php echo $lead->status == 'new' ? 'selected' : ''; ?>>جديد</option>
                        <option value="contacted" <?php echo $lead->status == 'contacted' ? 'selected' : ''; ?>>تم التواصل</option>
                        <option value="qualified" <?php echo $lead->status == 'qualified' ? 'selected' : ''; ?>>مؤهل (مهتم)</option>
                        <option value="lost" <?php echo $lead->status == 'lost' ? 'selected' : ''; ?>>غير مهتم / ضائع</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">تعيين إلى (موظف المبيعات)</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- غير معين --</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?php echo $user->id; ?>" <?php echo $lead->assigned_to == $user->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($user->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">ملاحظات إضافية</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($lead->notes); ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/lead/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>