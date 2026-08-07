<?php
// app/views/opportunity/edit.php
$opportunity = $opportunity ?? ($data['opportunity'] ?? null);
$customers = $customers ?? ($data['customers'] ?? []);
$users = $users ?? ($data['users'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل الفرصة: <?php echo htmlspecialchars($opportunity->title); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/opportunity/edit/<?php echo $opportunity->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">عنوان الفرصة <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($opportunity->title); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">العميل المستهدف <span class="required">*</span></label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- اختر العميل --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo $opportunity->customer_id == $c->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">المرحلة الحالية <span class="required">*</span></label>
                    <select name="stage" class="form-control" required>
                        <option value="qualification" <?php echo $opportunity->stage == 'qualification' ? 'selected' : ''; ?>>تأهيل (Qualification)</option>
                        <option value="proposal" <?php echo $opportunity->stage == 'proposal' ? 'selected' : ''; ?>>تقديم عرض (Proposal)</option>
                        <option value="negotiation" <?php echo $opportunity->stage == 'negotiation' ? 'selected' : ''; ?>>تفاوض (Negotiation)</option>
                        <option value="closed_won" <?php echo $opportunity->stage == 'closed_won' ? 'selected' : ''; ?>>تم الفوز (Closed Won)</option>
                        <option value="closed_lost" <?php echo $opportunity->stage == 'closed_lost' ? 'selected' : ''; ?>>تمت الخسارة (Closed Lost)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">القيمة المتوقعة (ر.س)</label>
                    <input type="number" name="estimated_value" step="0.01" class="form-control font-monospace" value="<?php echo $opportunity->estimated_value; ?>" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الإغلاق المتوقع</label>
                    <input type="date" name="expected_close_date" class="form-control" value="<?php echo $opportunity->expected_close_date ? date('Y-m-d', strtotime($opportunity->expected_close_date)) : ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">احتمالية الفوز (%)</label>
                    <input type="number" name="probability" min="0" max="100" class="form-control font-monospace" value="<?php echo $opportunity->probability; ?>" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">موظف المبيعات المسؤول</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- غير معين --</option>
                        <?php foreach($users as $u): ?>
                            <option value="<?php echo $u->id; ?>" <?php echo $opportunity->assigned_to == $u->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($u->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">الوصف والملاحظات</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($opportunity->description); ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/opportunity/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>