<?php
// app/views/followups/create.php
$leads =$data['leads'] ?? [];
?>

<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-calendar-plus text-primary"></i> جدولة متابعة جديدة للعميل</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/followup/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">العميل المحتمل (Lead) <span class="required">*</span></label>
                    <select name="lead_id" class="form-control" required>
                        <option value="">-- اختر العميل --</option>
                        <?php foreach($leads as$lead): ?>
                            <option value="<?php echo $lead->id; ?>"><?php echo htmlspecialchars($lead->name . ' - ' .$lead->company); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع المتابعة <span class="required">*</span></label>
                    <select name="type" class="form-control fw-bold" required>
                        <option value="call">📞 اتصال هاتفي</option>
                        <option value="meeting">🤝 اجتماع / زيارة</option>
                        <option value="email">📧 إرسال إيميل</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ ووقت المتابعة <span class="required">*</span></label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" required>
                </div>

                <div class="form-group full-width mt-2">
                    <label class="form-label">ملاحظات أو أجندة المتابعة</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="اكتب ما يجب مناقشته مع العميل..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> جدولة المتابعة</button>
            <a href="<?php echo URLROOT; ?>/followup/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>