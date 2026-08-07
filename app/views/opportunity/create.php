<?php
// app/views/opportunity/create.php
$customers = $data['customers'] ?? [];
$users = $data['users'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-bullseye text-primary"></i> إنشاء فرصة بيعية جديدة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/opportunity/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">عنوان الفرصة <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="مثال: توريد شاشات للفرع الجديد" required>
                </div>
                <div class="form-group">
                    <label class="form-label">العميل المستهدف <span class="required">*</span></label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- اختر العميل --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">المرحلة الحالية <span class="required">*</span></label>
                    <select name="stage" class="form-control" required>
                        <option value="qualification">تأهيل (Qualification)</option>
                        <option value="proposal">تقديم عرض (Proposal)</option>
                        <option value="negotiation">تفاوض (Negotiation)</option>
                        <option value="closed_won">تم الفوز (Closed Won)</option>
                        <option value="closed_lost">تمت الخسارة (Closed Lost)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">القيمة المتوقعة (ر.س)</label>
                    <input type="number" name="estimated_value" step="0.01" class="form-control font-monospace" value="0.00" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الإغلاق المتوقع</label>
                    <input type="date" name="expected_close_date" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">احتمالية الفوز (%)</label>
                    <input type="number" name="probability" min="0" max="100" class="form-control font-monospace" value="50" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">موظف المبيعات المسؤول</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- غير معين --</option>
                        <?php foreach($users as $u): ?>
                            <option value="<?php echo $u->id; ?>"><?php echo htmlspecialchars($u->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">الوصف والملاحظات</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الفرصة</button>
            <a href="<?php echo URLROOT; ?>/opportunity/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>