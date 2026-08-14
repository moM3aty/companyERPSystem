<?php
// app/views/tickets/create.php
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-ticket text-primary"></i> فتح تذكرة دعم فني</h3></div>
    <form action="<?php echo URLROOT; ?>/ticket/create" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="form-group mb-0">
                <label class="form-label">موضوع المشكلة <span class="required">*</span></label>
                <input type="text" name="subject" class="form-control" required>
            </div>
            <div class="form-group mb-0 mt-3">
                <label class="form-label">درجة الأهمية</label>
                <select name="priority" class="form-control fw-bold">
                    <option value="low">منخفضة (Low)</option>
                    <option value="medium" selected>متوسطة (Medium)</option>
                    <option value="high" class="text-danger">عاجلة / حرجة (High)</option>
                </select>
            </div>
            <div class="form-group mb-0 mt-3">
                <label class="form-label">تفاصيل المشكلة <span class="required">*</span></label>
                <textarea name="description" class="form-control" rows="5" required placeholder="يرجى وصف المشكلة بدقة..."></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال التذكرة</button>
            <a href="<?php echo URLROOT; ?>/ticket/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>