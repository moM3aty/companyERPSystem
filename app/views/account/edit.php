<?php
// app/views/account/edit.php
$acc = $acc ?? ($data['account'] ?? null);
$parents = $parents ?? ($data['parents'] ?? []);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل بيانات الحساب: <?php echo htmlspecialchars($acc->name); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/account/edit/<?php echo $acc->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group">
                    <label class="form-label">رقم الحساب (الكود) <span class="required">*</span></label>
                    <input type="text" name="code" class="form-control font-monospace" value="<?php echo htmlspecialchars($acc->code); ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">اسم الحساب <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($acc->name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">النوع (الطبيعة) <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="asset" <?php echo $acc->type == 'asset' ? 'selected' : ''; ?>>أصول (Assets)</option>
                        <option value="liability" <?php echo $acc->type == 'liability' ? 'selected' : ''; ?>>خصوم/التزامات (Liabilities)</option>
                        <option value="equity" <?php echo $acc->type == 'equity' ? 'selected' : ''; ?>>حقوق الملكية (Equity)</option>
                        <option value="revenue" <?php echo $acc->type == 'revenue' ? 'selected' : ''; ?>>إيرادات (Revenues)</option>
                        <option value="expense" <?php echo $acc->type == 'expense' ? 'selected' : ''; ?>>مصروفات (Expenses)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الحساب الرئيسي (الأب)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- حساب رئيسي مستقل --</option>
                        <?php foreach($parents as $p): ?>
                            <?php if($p->id !== $acc->id): ?>
                                <option value="<?php echo $p->id; ?>" <?php echo $acc->parent_id == $p->id ? 'selected' : ''; ?>><?php echo htmlspecialchars($p->code . ' - ' . $p->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i> الرصيد الحالي للحساب: <strong class="font-monospace fs-6"><?php echo number_format($acc->balance, 2); ?></strong>
                        <br>تعديل الرصيد الافتتاحي من هنا قد يؤثر على توازن ميزان المراجعة إذا لم تكن حذراً.
                    </div>
                    <label class="form-label">الرصيد</label>
                    <input type="number" name="balance" step="0.01" class="form-control font-monospace fw-bold" value="<?php echo $acc->balance; ?>" style="direction:ltr; text-align:right;">
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/account/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>