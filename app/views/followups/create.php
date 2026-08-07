<?php
// app/views/followups/create.php
$leads = $data['leads'] ?? [];
?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-plus text-primary"></i> جدولة متابعة لعميل محتمل</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/followup/create" method="POST">
        <div class="card-body">
            <div class="form-grid" style="grid-template-columns: 1fr;">
                <div class="form-group">
                    <label class="form-label">العميل المحتمل <span class="required">*</span></label>
                    <select name="lead_id" class="form-control" required>
                        <option value="">-- اختر العميل من القائمة --</option>
                        <?php foreach($leads as $lead): ?>
                            <option value="<?php echo $lead->id; ?>"><?php echo htmlspecialchars($lead->name . ($lead->company ? ' - ' . $lead->company : '')); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">نوع المتابعة <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="call">مكالمة هاتفية</option>
                        <option value="meeting">اجتماع مباشر / أونلاين</option>
                        <option value="email">إرسال بريد إلكتروني</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">التاريخ والوقت <span class="required">*</span></label>
                    <input type="datetime-local" name="scheduled_date" class="form-control font-monospace" required style="direction:ltr;">
                </div>
                <div class="form-group">
                    <label class="form-label">الهدف من المتابعة / الملاحظات</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="مثال: مناقشة التسعير بعد إرسال الكتالوج..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ وجدولة</button>
            <a href="<?php echo URLROOT; ?>/followup/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>