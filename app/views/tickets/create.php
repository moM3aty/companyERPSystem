<?php
// المسار: app/views/tickets/create.php
$customers = $data['customers'] ?? [];
$users = $data['users'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-headset text-primary"></i> فتح تذكرة شكوى أو دعم جديدة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/ticket/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">عنوان التذكرة (Subject) <span class="required">*</span></label>
                    <input type="text" name="subject" class="form-control" required placeholder="اكتب ملخصاً قصيراً للمشكلة...">
                </div>

                <div class="form-group">
                    <label class="form-label">العميل المرتبط (إن وجد)</label>
                    <select name="customer_id" class="form-control">
                        <option value="">-- تذكرة عامة أو داخلية --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تعيين إلى الموظف (Assigned To)</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- يحدد لاحقاً --</option>
                        <?php foreach($users as $u): ?>
                            <option value="<?php echo $u->id; ?>"><?php echo htmlspecialchars($u->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">مستوى الأولوية (Priority)</label>
                    <div class="d-flex align-items-center gap-4 mt-2">
                        <label class="d-flex align-items-center gap-2 cursor-pointer text-muted fw-bold"><input type="radio" name="priority" value="low"> منخفضة</label>
                        <label class="d-flex align-items-center gap-2 cursor-pointer text-info fw-bold"><input type="radio" name="priority" value="medium" checked> متوسطة</label>
                        <label class="d-flex align-items-center gap-2 cursor-pointer text-warning fw-bold"><input type="radio" name="priority" value="high"> عالية</label>
                        <label class="d-flex align-items-center gap-2 cursor-pointer text-danger fw-bold"><input type="radio" name="priority" value="urgent"> طارئة</label>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">وصف المشكلة بالتفصيل <span class="required">*</span></label>
                    <textarea name="description" class="form-control" required rows="5" placeholder="اكتب كل التفاصيل التي ستساعد في حل المشكلة بسرعة..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> فتح التذكرة</button>
            <a href="<?php echo URLROOT; ?>/ticket/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>